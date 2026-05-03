<?php

require_once("utils/bootstrap.php");

$templateParams["title"] = "Centro studio - Home";
$templateParams["in_user_page"] = false;
$templateParams["main"] = "template/index.php";
//$templateParams["js"] = [ "js/some_script.js" ];
$templateParams["css"] = [ "css/index.css" ];

require 'template/skeleton.php';

?>
