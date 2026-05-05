<?php

require_once("../utils/bootstrap.php");
$data = [];

if ($_GET["giveMePosts"] == 0) {
    //ritorna quanti post totali ci sono per l'utente loggato
    $data = $dbh->getPostsNumberOfEmail($_SESSION["email"]);
} else {
    //ritorna un “blocco” di post con paginazione
    $data = $dbh->getPostsOfEmail($_SESSION["email"], $_GET["offset"], $_GET["limit"]);
}

header('Content-Type: application/json');
echo json_encode($data);
?>