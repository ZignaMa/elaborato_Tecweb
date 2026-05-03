<?php

require_once "../utils/bootstrap.php";
$data = [];

if(isUserLoggedIn()) {
    if(isset($_GET["action"])){
        switch ($_GET["action"]) {
            case "1":
                $data = $dbh->getAllCourses($_SESSION["email"]);
                break;
            case "2":
                // uses italian param 'corso'
                $data = $dbh->getAllClasses($_SESSION["email"], $_GET["corso"]);
                break;
            case "3":
                // uses italian params 'corso' and 'classe'
                $data = $dbh->getYearsViaClassNameAndCourse($_SESSION["email"], $_GET["corso"], $_GET["classe"]);
                break;
            case "4":
                // uses italian params 'corso','classe','anno'
                $data = $dbh->getSectionOfUser($_SESSION["email"], $_GET["corso"], $_GET["classe"], $_GET["anno"]);
                break;
            case "5":
                $idPost = 0;
                if (isset($_FILES["imgpost"]) && $_FILES["imgpost"]["error"] === UPLOAD_ERR_OK) {
                    // POST fields expected in italian: corso, classe, sezione, anno
                    $idPost = $dbh->insertNewPost($_POST["corso"], $_POST["classe"], $_POST["sezione"], $_POST["anno"], htmlspecialchars($_POST["text"]), $_SESSION["email"]);
                    if($idPost != 0) {
                        $id_course = (int) $_POST["corso"];
                        $id_class = $dbh->getClassIdViaClassSectionYearCourse($_POST["classe"], $_POST["sezione"], $_POST["anno"], $id_course);
                        $percorsoDaSalvare = $id_course."/".$id_class."/".$_POST["anno"]."-".$_POST["sezione"]."/posts/".$idPost."/";

                        //prendere l'estensione del nome dell'immagine
                        $ext = "." . strtolower(pathinfo($_FILES["imgpost"]["name"], PATHINFO_EXTENSION));
                        $_FILES["imgpost"]["name"] = "0".$ext;
                        $percorsoFile = MEDIA_DIR.$percorsoDaSalvare;
                        if(!is_dir($percorsoFile)) {
                            mkdir($percorsoFile, 0700, true);
                        }

                        $nomeFile = $_FILES["imgpost"]["name"];
                        $result = uploadDocument($_FILES["imgpost"], $percorsoFile);
                        if(isset($result["success"])) {
                            $finalPathFile = $percorsoDaSalvare.$_FILES["imgpost"]["name"];
                            $dbh->assignPathToPost($idPost, $finalPathFile);
                        } else {
                            http_response_code(400);
                            echo json_encode($result);
                        }
                    }
                } else {
            $idPost = $dbh->insertNewPost($_POST["corso"], $_POST["classe"], $_POST["sezione"], $_POST["anno"], htmlspecialchars($_POST["text"]), $_SESSION["email"]);
                }
                $data["idPost"] = $idPost;
                break;
        }
    }
     else {
        $data = $dbh->getAllCourses();
    }
} else {
    if(isset($_GET["user"])) {
        $data = $dbh->getAllCourses();
    } else {
        header("Location: ../login.php");
    }
}
header("Content-Type: application/json");
echo json_encode($data);
?>
