<?php
require_once "../utils/bootstrap.php";

$data = null;

$return = [
    "success" => false,
    "data" => [],
    "message" => ""
];

function checkIsAValidYear($year) {
    $yearsArray = explode("-", $year);
    return intval($yearsArray[0]) == (intval($yearsArray[1]) - 1);
}

if (isAdmin()) {
    switch ($_GET["action"]) {
        case "change":
            // require canonical `email` param
            $emailParam = isset($_GET["email"]) ? $_GET["email"] : null;
            $data = $dbh->changeActiveStatus($emailParam);
            $return =  $data;
            break;
        case "clientsNumber":
            $data = $dbh->getClientsNumber();
            $return = $data;
            break;
        case "getClients":
            $data = $dbh->getAllClient($_GET["offset"], $_GET["limit"]);
            $return = $data;
            break;
        case "addCourse":
            $data = $dbh->addNewCourse($_POST["nome"]);
            if ($data) {
                $return = [
                    "success" => true,
                    "message" => ""
                ];
            } else {
                    $return = [
                        "success" => false,
                        "message" => "Impossibile aggiungere il corso"
                    ];
            }
            break;
        case "addYear":
            if (checkIsAValidYear($_POST["anno"])) {
                $data = $dbh->addNewSchoolYear($_POST["anno"]);
                if ($data) {
                    $return = [
                        "success" => true,
                        "message" => ""
                    ];
                } else {
                        $return = [
                            "success" => false,
                            "message" => "Impossibile aggiungere l'anno"
                        ];
                }
            } else {
                $return = [
                    "success" => false,
                    "message" => "La data non contiene anni consecutivi"
                ];
            }
            break;
        case "addClass":
            if ($_POST["assistente"] == 0) {
                $data = $dbh->addNewClass($_POST["corso"], $_POST["nome"], $_POST["anno"], $_POST["sezione"], $_POST["professore"]);
                if ($data) {
                    $return = [
                        "success" => true,
                        "message" => ""
                    ];
                } else {
                        $return = [
                            "success" => false,
                            "message" => "Impossibile aggiungere l'assistente"
                        ];
                }
            } else {
                $data = $dbh->addNewClass($_POST["corso"], $_POST["nome"], $_POST["anno"], $_POST["sezione"], $_POST["professore"], $_POST["assistente"]);
                if ($data) {
                    $return = [
                        "success" => true,
                        "message" => ""
                    ];
                } else {
                        $return = [
                            "success" => false,
                            "message" => "Impossibile aggiungere l'assistente"
                        ];
                }
            }
            break;
        case "addProfessor":
            $emailPost = isset($_POST["email"]) ? $_POST["email"] : null;
            $data = $dbh->addNewProfessor($emailPost, $_POST["nome"]);
            if ($data) {
                $return = [
                    "success" => true,
                    "message" => ""
                ];
            } else {
                    $return = [
                        "success" => false,
                        "message" => "Impossibile aggiungere il professore"
                    ];
            }
            break;
        case "addAssistant":
            $emailPost = isset($_POST["email"]) ? $_POST["email"] : null;
            $data = $dbh->addNewAssistant($emailPost, $_POST["nome"]);
            if ($data) {
                $return = [
                    "success" => true,
                    "message" => ""
                ];
            } else {
                $return = [
                    "success" => false,
                    "message" => "Failed to add a new assistant"
                ];
            }
            break;
        case "addAdmin":
            $emailPost = isset($_POST["email"]) ? $_POST["email"] : null;
            $nomeUtentePost = isset($_POST["nome_utente"]) ? $_POST["nome_utente"] : "";
            $data = $dbh->addNewUser($emailPost, $_POST["password"], $nomeUtentePost, 0, 1);
            if ($data) {
                $return = [
                    "success" => true,
                    "message" => ""
                ];
            } else {
                    $return = [
                        "success" => false,
                        "message" => "Impossibile aggiungere l'amministratore"
                    ];
            }
            break;
        //js
        case "getClassesByCourseId":
            $courseId = isset($_GET['corso_id']) ? $_GET['corso_id'] : null;
            if (!$courseId) {
                 $return = ["success" => false, "message" => "manca corso_id", "data" => []];
            } else {
                $data = $dbh->getClassesByCourseID($courseId);
                $return = $data;
            }
            break;
    }
} else {
    header("Location: login.php");
}

header('Content-Type: application/json');
echo json_encode($return);

?>