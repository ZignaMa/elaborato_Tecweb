<?php
require_once "utils/bootstrap.php";

if (!isUserLoggedIn() || !isAdmin()) {
    header("Location: login.php");
}

$templateParams["title"] = "Centro studio - Admin";
$templateParams["main"] = "template/admin.php";
$templateParams["css"] = array("css/admin.css");
$templateParams["js"] = array("js/admin.js", "js/admin-admins.js", "js/admin-clients.js", "js/admin-classes.js");

require "template/skeleton.php";

?>
