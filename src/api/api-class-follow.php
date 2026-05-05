<?php
require_once "../utils/bootstrap.php";
$data = [];

$classId = null;
if (isset($_GET['classe_id'])) {
    $classId = $_GET['classe_id'];
}

if (!$classId) {
    http_response_code(400);
    $data = ["error" => "missing class identifier 'classe_id'"];
} else {
    if (isset($_GET["getStatus"]) && $_GET["getStatus"]==1) {
        $data = $dbh->getFollowStatusOfUserOfClass($_SESSION["email"], $classId);
    } else if (isset($_GET["followOperation"]) && $_GET["followOperation"] == 1) {
        $data = $dbh->userStartFollowClass($_SESSION["email"], $classId);
    } else {
        $data = $dbh->userEndFollowClass($_SESSION["email"], $classId);
    }
}


header('Content-Type: application/json');
echo json_encode($data);

?>