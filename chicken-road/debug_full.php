<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Full Debug Test\n";
echo "URL: " . $_SERVER['REQUEST_URI'] . "\n";

if( !defined('BASE_DIR') ){ 
    define('BASE_DIR', dirname(__FILE__)."/"); 
}

include_once BASE_DIR ."init.php"; 
include_once BASE_DIR ."router.php"; 

echo "After router:\n";
echo "- ISAPI: " . (defined('ISAPI') ? (ISAPI ? 'true' : 'false') : 'Not defined') . "\n";
echo "- CONTROLLER: " . (defined('CONTROLLER') ? CONTROLLER : 'Not defined') . "\n";
echo "- ACTION: " . (defined('ACTION') ? ACTION : 'Not defined') . "\n";

include_once BASE_DIR ."common.php"; 

if( !defined('TPL') ){ define('TPL', "main"); } 

echo "- TPL: " . (defined('TPL') ? TPL : 'Not defined') . "\n";
echo "- TPL file: " . TPL_DIR . TPL . TPL_EXT . "\n";
echo "- TPL exists: " . (file_exists(TPL_DIR . TPL . TPL_EXT) ? 'Yes' : 'No') . "\n";

if (isset($_SESSION['user'])) {
    echo "User session:\n";
    print_r($_SESSION['user']);
} else {
    echo "No user session\n";
}
?>
