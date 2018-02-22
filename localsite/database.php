<?php
/* Define the absolute paths for configured directories */
define('APPDIR', realpath(__DIR__.'/../app/').'/');
define('SYSTEMDIR', realpath(__DIR__.'/../system/').'/');
define('PUBLICDIR', realpath(__DIR__).'/');
define('ROOTDIR', realpath(__DIR__.'/../').'/');

/* load Composer Autoloader */
if (file_exists(ROOTDIR.'vendor/autoload.php')) {
    require ROOTDIR.'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

/* Start the Session */
session_start();

/* Error Settings */
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* Make sure Config File Exists */
if (is_readable(APPDIR.'Config.php')) {

  new \App\Config();

}else{
  echo "Config.php File Not Found";
}

$host = DB_HOST;
$db   = DB_NAME;
$user = DB_USER;
$pass = DB_PASS;
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);

?>
