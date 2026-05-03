<?php
require_once "../utils/bootstrap.php";

$resultOperation = [
    "success" => true,
    "errorId" => 0,
    "error" => ""
];

$coId = isset($_GET['corso_id']) ? $_GET['corso_id'] : null;
$classId = isset($_GET['classe_id']) ? $_GET['classe_id'] : null;
$anno = isset($_GET['anno']) ? $_GET['anno'] : null;
$sezione = isset($_GET['sezione']) ? $_GET['sezione'] : null;

if (!$classId || !$coId || !$anno || !$sezione) {
    http_response_code(400);
    $resultOperation["success"] = false;
    $resultOperation["error"] = "missing parameters";
    header('Content-Type: application/json');
    echo json_encode($resultOperation);
    exit;
}

$filesInformazioni = isset($_FILES["newResources"]) ? $_FILES["newResources"] : null;
$dbPath = $coId . "/" . $classId . "/" . $anno . "-" . $sezione . "/";
$percorsoFile = filePathCreator($coId, $classId, $sezione, $anno);

function filePathCreator($idCorso, $idClasse, $sezione, $anno)
{
    return MEDIA_DIR . $idCorso . "/" . $idClasse . "/" . $anno . "-" . $sezione . "/";
}

if (isUserLoggedIn()) {
    if ($filesInformazioni === null) {
        $resultOperation["success"] = false;
        $resultOperation["error"] = "no files provided";
    } else {
        foreach ($filesInformazioni["name"] as $i => $nome) {
            $unsuccessfulInsert = true;
            $type = pathinfo($nome, PATHINFO_EXTENSION);
            $nomeSenzaTipo = pathinfo($nome, PATHINFO_FILENAME);
            $nomeReale = $nome;
            if ($filesInformazioni["error"][$i] === UPLOAD_ERR_OK) {
                if (!is_dir($percorsoFile)) {
                    mkdir($percorsoFile, 0700, true);
                }
                for ($j = 0; $unsuccessfulInsert; $j++) {
                    $numberOfCopyName = $dbh->pathReplicateCheck($dbPath . $nomeReale);
                    if ($numberOfCopyName[0]["count"] == 0) {
                        if (!is_dir($percorsoFile) || !is_writable($percorsoFile)) {
                            $resultOperation["success"] = false;
                            $resultOperation["error"] =  "Upload directory is not accessible";
                            $resultOperation["errorId"] = 1;
                        }
                        if (!move_uploaded_file($filesInformazioni["tmp_name"][$i], $percorsoFile . $nomeReale)) {
                            $resultOperation["success"] = false;
                            $resultOperation["error"] = "File upload failed";
                            $resultOperation["errorId"] = 2;
                        }
                        $dbh->insertUnlinkedResources($_SESSION["email"], $classId, $dbPath . $nomeReale);
                        $unsuccessfulInsert = false;
                    } else if ($numberOfCopyName[0]["count"] == 1) {
                        $nomeReale = $nomeSenzaTipo . ($j + 1) . "." . $type;
                    } else {
                        $resultOperation["success"] = false;
                        $resultOperation["error"] = "DB have a name that is repeat more than one time";
                        $resultOperation["errorId"] = 3;
                        $unsuccessfulInsert = false;
                    }
                }
            } else {
                $resultOperation["success"] = false;
                $resultOperation["error"] = "There was an error during the upload of a file";
                $resultOperation["errorId"] = 4;

            }
        }
    }
} else {
    header('Location: ../login.php');
}

header('Content-Type: application/json');
echo json_encode($resultOperation);
?>