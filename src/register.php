<?php

require_once "utils/bootstrap.php";

if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["corso"])) {
    $user = $dbh->getUserViaEmail($_POST["email"]);
    if (count($user) == 0) {
    $idCorso = extractId($_POST["corso"]);
    $nome_utentePost = isset($_POST["nome_utente"]) ? $_POST["nome_utente"] : "";
    $risultato = $dbh->addNewUser($_POST["email"], $_POST["password"], $nome_utentePost, $idCorso);
        header("Location: login.php");
    } else {
        $templateParams["error"] = "Error: a user with this email already exists";
    }
}

if(isUserLoggedIn()) {
    header("Location: login.php");
} else {
    $templateParams["title"] = "Centro studio - Registrazione";
    $templateParams["main"] = "register-form.php";
    $templateParams["js"] = array();
    array_push($templateParams["js"], "js/courses.js");
}

$templateParams["css"] = [ "css/logreg.css" ];

require 'template/skeleton.php';

?>
