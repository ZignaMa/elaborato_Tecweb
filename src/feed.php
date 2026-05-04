<?php

require_once "utils/bootstrap.php";

if (!isUserLoggedIn()) {
    header("Location: login.php");
}
$templateParams["title"] = "Centro studio - Bacheca";
$templateParams["main"] = "template/feed.php";
$templateParams["js"] = ["js/feed.js"];
$templateParams["css"] = ["css/feed.css"];

require "template/skeleton.php";

?>
