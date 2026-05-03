<?php

require_once "utils/bootstrap.php";

if (isset($_POST["email"]) && isset($_POST["password"])) {
    $login_result = $dbh->checkLogin($_POST["email"], $_POST["password"]);
    if (count($login_result) == 0) {
        // Login failed
        $templateParams["error"] = "Errore: nome utente o password errati";
    } elseif ($login_result[0]["active"] === 0) {
        // User inactive
        $templateParams["error"] = "Errore: il tuo account è sospeso";
    } else {
        registerLoggedUser($login_result[0]);
    }
}

if (isUserLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin.php");
    } else {
    header("Location: feed.php");
    }
} else {
    $templateParams["title"] = "Centro studio - Login";
    $templateParams["main"] = "login-form.php";
}

$templateParams["css"] = [ "css/logreg.css" ];

require "template/skeleton.php";

?>
