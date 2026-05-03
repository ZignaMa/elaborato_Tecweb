<?php

require_once("../utils/bootstrap.php");
$data = [];

if ($_GET["giveMePosts"] == 0) {
    $data = $dbh->getPostsNumberOfEmail($_SESSION["email"]);
} else {
    $data = $dbh->getPostsOfEmail($_SESSION["email"], $_GET["offset"], $_GET["limit"]);
}

header('Content-Type: application/json');
echo json_encode($data);
?>