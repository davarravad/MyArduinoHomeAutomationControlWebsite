<?php
/**
* Arduino Smart Home Control Console - Database and Settings File
*
*
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 1.1
*/


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

  /* Load Config Settings */
  new \App\Config();

  /* Load Site Settings From Database */
  new \App\System\LoadSiteSettings();


}else{
  /* Show error if Config.php is not found */
  echo "Config.php File Not Found";
}

/* Setup MySQL Database */
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

/* Check for House ID and Get House Token from Database */
if(isset($_REQUEST['house_id'])){
    $house_id = $_REQUEST['house_id'];
    /* Get House Token From Database */
    $stmt = $pdo->prepare('SELECT house_token, email_enable_doors, email_doors_minutes FROM uap4_hc_house WHERE house_id = :house_id LIMIT 1');
    $stmt->execute(['house_id' => $house_id]);
    $data = $stmt->fetch();
    $db_house_token = $data["house_token"];
    $db_email_enable_doors = $data["email_enable_doors"];
    $db_email_doors_minutes = $data["email_doors_minutes"];
}else{
    /* House ID Not Found */
    echo "<ERROR-N0-HOUSE-ID>";
}

?>
