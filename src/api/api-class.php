<?php

require_once "../utils/bootstrap.php";
$data = [];

$classId = isset($_GET['classe_id']) ? $_GET['classe_id'] : null;

switch (isset($_GET["action"]) ? $_GET["action"] : null) {
    case 1:
        //ritorna quanti post ci sono per quella classe
        if (!$classId) {
            http_response_code(400);
            $data = ["error" => "missing class identifier"];
        } else {
            $data = $dbh->getPostsNumberOfClass($classId);
        }
        break;
    case 2:
        //ritorna quante risorse ci sono per quella classe
        if (!$classId) {
            http_response_code(400);
            $data = ["error" => "missing class identifier"];
        } else {
            $data = $dbh->getResourcesNumberOfClass($classId);
        }
        break;
    case 3:
        // ritorna la lista dei post
        if (!$classId) {
            http_response_code(400);
            $data = ["error" => "missing class identifier"];
        } else {
            $limit = isset($_GET['limit']) ? $_GET['limit'] : null;
            $offset = isset($_GET['offset']) ? $_GET['offset'] : null;
            $data = $dbh->getPostsOfClass($classId, $limit, $offset);
        }
        break;
    case 4:
        // ritorna la lista delle risorse
        if (!$classId) {
            http_response_code(400);
            $data = ["error" => "missing class identifier"];
        } else {
            $limit = isset($_GET['limit']) ? $_GET['limit'] : null;
            $offset = isset($_GET['offset']) ? $_GET['offset'] : null;
            $data = $dbh->getResourcesOfClass($classId, $limit, $offset);
        }
        break;
    case 5:
        // require canonical `percorso` param
        //elimina una risorsa “non collegata” usando il parametro percorso
        $pathParam = isset($_GET["percorso"]) ? $_GET["percorso"] : null;
        if (!$pathParam) {
            http_response_code(400);
            $data = ["error" => "missing percorso parameter"];
        } else {
            $data = $dbh->deleteUnlinkedResources($pathParam);
        }
        break;
}

header('Content-Type: application/json');
echo json_encode($data);

?>