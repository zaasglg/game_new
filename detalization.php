<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=Conéctese");
    exit();
}

require 'db.php';
require 'detalization_db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email, deposit, country, bonificaciones FROM users WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: index.php?error=Usuario no encontrado");
    exit();
}

$email = $user['email'];
$deposit = $user['deposit'];
$country = $user['country'];
$bonificaciones = $user['bonificaciones'] ?? 0;
$is_admin = ($email === 'admin');

$currency_map = [
    'Argentina' => 'ARS', 'Bolivia' => 'BOB', 'Brazil' => 'BRL', 'Chile' => 'CLP',
    'Colombia' => 'COP', 'Costa Rica' => 'CRC', 'Cuba' => 'CUP', 'Dominican Republic' => 'DOP',
    'Ecuador' => 'USD', 'El Salvador' => 'USD', 'Guatemala' => 'Q', 'Haiti' => 'HTG',
    'Honduras' => 'HNL', 'Mexico' => 'MXN', 'Nicaragua' => 'NIO', 'Panama' => 'USD',
    'Paraguay' => 'PYG', 'Peru' => 'PEN', 'Puerto Rico' => 'USD', 'Uruguay' => 'UYU',
    'Venezuela' => 'VES'
];

$currency = $currency_map[$country] ?? 'USD';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalization - Valor Casino</title>
    <link rel="stylesheet" href="./css/account.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <style>
        .transaction-list { margin: 20px 0; }
        .transaction-item { 
            background: #fff; 
            border: 1px solid #ddd; 
            margin: 10px 0; 
            padding: 15px; 
            border-radius: 5px; 
        }
        .transaction-header { font-weight: bold; margin-bottom: 10px; }
        .transaction-details { display: flex; justify-content: space-between; }
        .status-esperando { color: #ff9800; }
        .status-completed { color: #4caf50; }
        .status-rejected { color: #f44336; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Historial de Transacciones</h1>
        <div id="transaction-list" class="transaction-list">
            <div class="text-center">Cargando transacciones...</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadTransactions();
        });

        function loadTransactions() {
            fetch('detalization_db.php', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayTransactions(data.transactions);
                } else {
                    document.getElementById('transaction-list').innerHTML = 
                        '<div class="alert alert-danger">Error al cargar las transacciones</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('transaction-list').innerHTML = 
                    '<div class="alert alert-danger">Error de conexión</div>';
            });
        }

        function displayTransactions(transactions) {
            const container = document.getElementById('transaction-list');
            
            if (!transactions || transactions.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No hay transacciones disponibles</div>';
                return;
            }

            let html = '';
            transactions.forEach(transaction => {
                const statusClass = `status-${transaction.estado}`;
                html += `
                    <div class="transaction-item">
                        <div class="transaction-header">
                            Transacción #${transaction['transacción_number']}
                        </div>
                        <div class="transaction-details">
                            <div>
                                <strong>Fecha:</strong> ${transaction.transacciones_data}<br>
                                <strong>Monto:</strong> ${transaction.transacciones_monto} <?php echo $currency; ?><br>
                                <strong>Método:</strong> ${transaction['método_de_pago']}
                            </div>
                            <div>
                                <span class="badge ${statusClass}">${transaction.estado}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
    </script>
</body>
</html>