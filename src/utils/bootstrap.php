<?php
session_start();

define("ROOT_DIR", dirname(__DIR__, 1) . "/");
define("MEDIA_DIR", ROOT_DIR . "/uploads/media/");

define("REL_PATH", str_replace("\\", "/", str_replace(realpath($_SERVER["DOCUMENT_ROOT"]), "", ROOT_DIR)));
define("REL_PATH_MEDIA", REL_PATH . "/uploads/media/");

require_once(__DIR__."/functions.php");
require_once(__DIR__."/../db/db.php");

// Read DB connection parameters from environment with sensible defaults.
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'centrostudio';
$dbPort = getenv('DB_PORT') ?: 3306;

$dbh = new DatabaseHelper($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
?>