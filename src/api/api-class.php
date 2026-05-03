<?php

require_once "../utils/bootstrap.php";
$data = [];

// require italian param 'classe_id'
$classId = isset($_GET['classe_id']) ? $_GET['classe_id'] : null;

switch (isset($_GET["action"]) ? $_GET["action"] : null) {
    case 1:
        if (!$classId) {
            http_response_code(400);
            $data = ["error" => "missing class identifier"];
        } else {
            $data = $dbh->getPostsNumberOfClass($classId);
        }
        break;
    case 2:
        if (!$classId) {
            http_response_code(400);
            $data = ["error" => "missing class identifier"];
        } else {
            $data = $dbh->getResourcesNumberOfClass($classId);
        }
        break;
    case 3:
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