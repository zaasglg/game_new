<?php
require_once 'db.php';

class StageBalanceUpdater {
    private $conn;
    private $shouldSkipCheck = false;
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }
    
    public function updateForUser($userId) {
        try {
            // Получаем данные пользователя
            $stmt = $this->conn->prepare("
                SELECT verification_start_date, stage_balance, stage, deposit, country 
                FROM users 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Пользователь не найден'];
            }
            
            // Если баланс уже 0 и stage уже 'supp', устанавливаем флаг и ничего не делаем
            if ($user['stage_balance'] <= 0 && $user['stage'] === 'supp') {
                $this->shouldSkipCheck = true;
                return ['success' => false, 'message' => 'Баланс уже нулевой и stage установлен в supp - проверка пропущена'];
            }
            
            // Если флаг установлен, пропускаем все дальнейшие проверки
            if ($this->shouldSkipCheck) {
                return ['success' => false, 'message' => 'Проверка пропущена (баланс уже нулевой и stage supp)'];
            }
            
            // Остальной код остается без изменений
            if ($user['stage_balance'] <= 0) {
                if ($user['stage'] === 'verif' || $user['stage'] === 'verif2') { // Изменено здесь
                    $this->updateStageToSupp($userId);
                    return [
                        'success' => true, 
                        'message' => 'Stage обновлен в supp (был '.$user['stage'].')', 
                        'new_balance' => 0
                    ];
                }
                return ['success' => true, 'message' => 'Баланс нулевой, но stage не verif/verif2 - не изменяем', 'new_balance' => 0];
            }
            
            if (empty($user['verification_start_date'])) {
                return ['success' => false, 'message' => 'Не указана дата начала верификации'];
            }
            
            $txStmt = $this->conn->prepare("
                SELECT id, amount_usd 
                FROM historial 
                WHERE user_id = ? 
                AND estado IN ('completed', 'esperando') 
                AND transacciones_data >= ?
                AND amount_usd > 0
                AND (stage_processed = 0 OR stage_processed IS NULL)
                ORDER BY transacciones_data ASC
            ");
            $txStmt->execute([$userId, $user['verification_start_date']]);
            error_log("Query executed for user $userId with date {$user['verification_start_date']}");
            error_log("Found " . $txStmt->rowCount() . " transactions");
            $transactions = $txStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $remainingBalance = $user['stage_balance'];
            $processedIds = [];
            $totalDeducted = 0;
            $totalAddedToDeposit = 0;
            
            foreach ($transactions as $tx) {
                if ($remainingBalance <= 0) break;
                
                $deductAmount = min($tx['amount_usd'], $remainingBalance);
                $totalAddedToDeposit += $tx['amount_usd'];
                $remainingBalance -= $deductAmount;
                $totalDeducted += $deductAmount;
                $processedIds[] = $tx['id'];
            }
            
            if (!empty($processedIds)) {
                $this->conn->beginTransaction();
                
                try {
                    $addDepositStmt = $this->conn->prepare("
                        UPDATE users 
                        SET deposit = deposit + ? 
                        WHERE user_id = ?
                    ");
                    $addDepositStmt->execute([$totalAddedToDeposit, $userId]);
                    
                    $updateStageStmt = $this->conn->prepare("
                        UPDATE users 
                        SET stage_balance = ? 
                        WHERE user_id = ?
                    ");
                    $updateStageStmt->execute([$remainingBalance, $userId]);
                    
                    if ($remainingBalance <= 0 && ($user['stage'] === 'verif' || $user['stage'] === 'verif2')) { // Изменено здесь
                        $this->updateStageToSupp($userId);
                    }
                    
                    $placeholders = implode(',', array_fill(0, count($processedIds), '?'));
                    $markStmt = $this->conn->prepare("
                        UPDATE historial 
                        SET stage_processed = 1 
                        WHERE id IN ($placeholders)
                    ");
                    $markStmt->execute($processedIds);
                    
                    $this->conn->commit();
                    
                    error_log("User $userId: Added $totalAddedToDeposit to deposit, deducted $totalDeducted from stage_balance");
                    
                    return [
                        'success' => true,
                        'new_balance' => $remainingBalance,
                        'deducted_from_stage' => $totalDeducted,
                        'added_to_deposit' => $totalAddedToDeposit,
                        'processed_count' => count($processedIds),
                        'stage_updated' => ($remainingBalance <= 0 && ($user['stage'] === 'verif' || $user['stage'] === 'verif2')),
                        'message' => "Обработано транзакций: " . count($processedIds) . ", списано со stage: $totalDeducted, добавлено к депозиту: $totalAddedToDeposit"
                    ];
                    
                } catch (Exception $e) {
                    $this->conn->rollBack();
                    error_log("Transaction error for user $userId: " . $e->getMessage());
                    return ['success' => false, 'error' => "Transaction error: " . $e->getMessage()];
                }
            }
            
            return ['success' => false, 'message' => 'Нет новых транзакций для обработки'];
            
        } catch (Exception $e) {
            error_log("Balance update error for user $userId: " . $e->getMessage());
            return ['success' => false, 'error' => "Balance update error: " . $e->getMessage()];
        }
    }
    
    private function updateStageToSupp($userId) {
        // Проверяем текущий stage пользователя
        $checkStmt = $this->conn->prepare("
            SELECT stage FROM users WHERE user_id = ?
        ");
        $checkStmt->execute([$userId]);
        $currentStage = $checkStmt->fetchColumn();
        
        // Обновляем только если текущий stage 'verif' или 'verif2'
        if ($currentStage === 'verif' || $currentStage === 'verif2') { // Изменено здесь
            $stmt = $this->conn->prepare("
                UPDATE users 
                SET stage = 'supp' 
                WHERE user_id = ?
            ");
            $result = $stmt->execute([$userId]);
            error_log("Updated stage from $currentStage to supp for user $userId: " . ($result ? 'success' : 'failed'));
            return $result;
        }
        
        error_log("Not updating stage for user $userId - current stage is $currentStage (expected verif or verif2)");
        return false;
    }
}

// API endpoint
if (isset($_GET['action']) && $_GET['action'] == 'check_stage_balance' && isset($_GET['user_id'])) {
    header('Content-Type: application/json');
    $updater = new StageBalanceUpdater($conn);
    $result = $updater->updateForUser($_GET['user_id']);
    echo json_encode($result);
    exit;
}