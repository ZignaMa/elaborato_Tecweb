<?php

require_once 'utils/bootstrap.php';

if (!isUserLoggedIn()) {
    header("Location: ../login.php");
}
$templateParams["title"] = "Centro studio - Classe";
$templateParams["main"] = "template/class.php";
$templateParams["css"] = ["css/class.css"];
$templateParams["js"] = [
    "js/class.js",
    "js/class-follow.js",
    "js/class-resources.js",
];

require 'template/skeleton.php';

?>
