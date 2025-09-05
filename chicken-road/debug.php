<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP Test Page\n";
echo "Current directory: " . __DIR__ . "\n";
echo "Base directory exists: " . (defined('BASE_DIR') ? 'Yes' : 'No') . "\n";

try {
    include_once 'init.php';
    echo "Init loaded successfully\n";
    
    echo "Constants defined:\n";
    echo "- CONTROLLER: " . (defined('CONTROLLER') ? CONTROLLER : 'Not defined') . "\n";
    echo "- ACTION: " . (defined('ACTION') ? ACTION : 'Not defined') . "\n";
    echo "- TPL: " . (defined('TPL') ? TPL : 'Not defined') . "\n";
    echo "- TPL_DIR: " . (defined('TPL_DIR') ? TPL_DIR : 'Not defined') . "\n";
    
    if (defined('TPL_DIR') && defined('TPL')) {
        $tpl_file = TPL_DIR . TPL . '.tpl.php';
        echo "Template file: $tpl_file\n";
        echo "Template exists: " . (file_exists($tpl_file) ? 'Yes' : 'No') . "\n";
    }
    
    echo "Session data:\n";
    echo "- User UID: " . (isset($_SESSION['user']['uid']) ? $_SESSION['user']['uid'] : 'Not set') . "\n";
    echo "- User balance: " . (isset($_SESSION['user']['balance']) ? $_SESSION['user']['balance'] : 'Not set') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
