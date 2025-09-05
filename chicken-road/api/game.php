<?php
session_start();
require_once "../init.php";

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'place_bet':
                $bet_amount = (float)($_POST['bet_amount'] ?? 0);
                $difficulty = $_POST['difficulty'] ?? 'easy';
                
                if ($bet_amount <= 0) {
                    throw new Exception('Invalid bet amount');
                }
                
                if (!isset($_SESSION['user']['uid'])) {
                    throw new Exception('User not found');
                }
                
                // Check balance
                $user = Users::GI()->getById($_SESSION['user']['uid']);
                if (!$user || $user['balance'] < $bet_amount) {
                    throw new Exception('Insufficient balance');
                }
                
                // Create new game
                $game_data = [
                    'user_id' => $_SESSION['user']['uid'],
                    'bet' => $bet_amount,
                    'difficulty' => $difficulty,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $game_id = Games::GI()->add($game_data);
                
                if ($game_id) {
                    // Update user balance
                    Users::GI()->updateBalance($_SESSION['user']['uid'], -$bet_amount);
                    $_SESSION['user']['balance'] -= $bet_amount;
                    
                    echo json_encode([
                        'success' => true,
                        'game_id' => $game_id,
                        'balance' => $_SESSION['user']['balance']
                    ]);
                } else {
                    throw new Exception('Failed to create game');
                }
                break;
                
            case 'cashout':
                $game_id = (int)($_POST['game_id'] ?? 0);
                $multiplier = (float)($_POST['multiplier'] ?? 1.0);
                
                if (!$game_id) {
                    throw new Exception('Invalid game ID');
                }
                
                $game = Games::GI()->getById($game_id);
                if (!$game || $game['user_id'] != $_SESSION['user']['uid']) {
                    throw new Exception('Game not found');
                }
                
                if ($game['status'] != 'active') {
                    throw new Exception('Game is not active');
                }
                
                $win_amount = $game['bet'] * $multiplier;
                
                // Update game
                Games::GI()->update($game_id, [
                    'status' => 'won',
                    'multiplier' => $multiplier,
                    'win' => $win_amount,
                    'finished_at' => date('Y-m-d H:i:s')
                ]);
                
                // Update user balance
                Users::GI()->updateBalance($_SESSION['user']['uid'], $win_amount);
                $_SESSION['user']['balance'] += $win_amount;
                
                echo json_encode([
                    'success' => true,
                    'win_amount' => $win_amount,
                    'balance' => $_SESSION['user']['balance']
                ]);
                break;
                
            default:
                throw new Exception('Unknown action');
        }
    } else {
        throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
