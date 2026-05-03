<?php

session_start();
require_once("utils/functions.php");
logout();
header("Location: index.php");

?>
