<?php

require_once "utils/bootstrap.php";

// Ensure the requester is logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
}

// Load user info
// Require `email` query param when requesting another user's page. Otherwise use session email.
if (isset($_GET["email"]) && strlen($_GET["email"]) != 0) {
    $email = filter_var($_GET["email"], FILTER_SANITIZE_EMAIL);
    if ($email === false) {
        http_response_code(400);
        echo "Error: invalid email";
        exit();
    }
    $templateParams["in_user_page"] = false;
} else {
    // Use session email if available; otherwise guard against missing session keys
    $email = isset($_SESSION["email"]) ? $_SESSION["email"] : null;
    $templateParams["in_user_page"] = true;
}

// Ensure $email is a valid non-empty string before calling DB helper
if (!is_string($email) || strlen($email) === 0) {
    // Not authorized or bad request: redirect to login or return an error
    http_response_code(400);
    echo "Error: missing email";
    exit();
}

$users = $dbh->getUserViaEmail($email);

if (count($users) <= 0) {
    http_response_code(400);
    echo "Error: invalid email";
    exit();
}
$templateParams["user"] = $users[0];

$templateParams["title"] = "Centro studio - " . $templateParams["user"]["nome_utente"];
$templateParams["main"] = "template/user.php";
$templateParams["css"] = ["css/user.css"];
$templateParams["js"] = [ "js/user.js" ];

require "template/skeleton.php";

?>
