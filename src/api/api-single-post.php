<?php

require_once "../utils/bootstrap.php";

if(isUserLoggedIn()) {
    if(isset($_GET["action"])){
        switch ($_GET["action"]) {
            case "0": //farsi dire se l'utente è un admin
                $data = isAdmin();
                break;
            case "1": //prendere informazioni iniziali del post
                $idpostInt = (int) $_GET["idpost"];
                $data = $dbh->getInitialInfoOfPostViaId($idpostInt);
                break;
            case "2": //prendere i prossimi n commenti
                $idpostInt = (int) $_GET["idpost"];
                $idComment = (int) $_GET["idComment"];
                $maxComments = (int) $_GET["maxComments"];
                $data = "";
                $data = $dbh->getNextComments($idpostInt, $idComment, $maxComments, $_GET["dateAndHourComment"]);
                break;
            case "3": //inserire un nuovo commento
                $data = [];
                $idComment = 0;
                $idPostInt = (int) $_GET["idpost"];
                if (isset($_FILES["imgcomment"]) && $_FILES["imgcomment"]["error"] === UPLOAD_ERR_OK) {
                    $idComment = $dbh->insertNewComment($_SESSION["email"], $idPostInt, htmlspecialchars($_POST["text"]));
                    $percorsoDaSalvare = createPathFromComment($idPostInt, $dbh, $_FILES["imgcomment"]["name"]);
                    $nuovoNomeFile = getFileNameFromPath($percorsoDaSalvare);
                    $percorsoFile = MEDIA_DIR.dirname($percorsoDaSalvare)."/";
                    if(!is_dir($percorsoFile)) {
                        mkdir($percorsoFile, 0700, true);
                    }
                    $result = uploadDocument($_FILES["imgcomment"], $percorsoFile, $nuovoNomeFile);
                    if(isset($result["success"])) {
                        $dbh->assignPathToComment($idComment, $percorsoDaSalvare);
                    } else {
                        http_response_code(400);
                        echo json_encode($result);
                        exit;
                    }
                } else {
                    $idComment = $dbh->insertNewComment($_SESSION["email"], $idPostInt, htmlspecialchars($_POST["text"]));
                }
                $data["idComment"] = $idComment;
                break;
            case "4": //cancellazione di un commento
                if(isAdmin()) {
                    $idComment = (int) $_GET["idComment"];
                    $imgPath = $dbh->getPathOfComment($idComment);
                    if($imgPath != ""){
                        $percorsoFileDaRimuovere = MEDIA_DIR.$imgPath;
                        if(file_exists($percorsoFileDaRimuovere)){
                            unlink($percorsoFileDaRimuovere);
                        }
                    }
                    $dbh->removeComment($idComment);
                }
                break;
            case "5": //mdofica testo di un commento
                if(isAdmin()) {
                    $idComment = (int) $_GET["idComment"];
                    $data = "nuovo testo del commento".$dbh->updateTextOfComment($idComment, htmlspecialchars($_POST["text"]));
                }
                break;
            case "6":
                if(isAdmin()) {
                    $idComment = (int) $_GET["idComment"];
                    $imgPath = $dbh->getPathOfComment($idComment);
                    $data = $dbh->removePathOfComment($idComment);
                    if($imgPath != ""){
                        $percorsoFileDaRimuovere = MEDIA_DIR.$imgPath;
                        if(file_exists($percorsoFileDaRimuovere)){
                            unlink($percorsoFileDaRimuovere);
                        }
                    }
                }
                break;
            case "7": //eliminazione di un post
                $idPost = (int) $_GET["idPost"];
                $imgPostPath = $dbh->getPathOfPost($idPost);
                if($imgPostPath != ""){
                    $percorsoFilePost = MEDIA_DIR.$imgPostPath;
                    if(file_exists($percorsoFilePost)){
                        unlink($percorsoFilePost);
                        $dbh->removePathOfPost($idPost);
                    }
                }
                $allCommentsIdAndPath = $dbh->getAllCommentsViaPostId($idPost);
                foreach($allCommentsIdAndPath as $commento){
                    $imgCommentPath = $commento["percorso"];
                    if(!empty($imgCommentPath)){
                        $percorsoFileCommento = MEDIA_DIR.$imgCommentPath;
                        if(file_exists($percorsoFileCommento)){
                            unlink($percorsoFileCommento);
                        }
                    }
                    $idComment = (int) $commento["id"];
                    $dbh->removeComment($idComment);
                }
                $dbh->removePost($idPost);
                break;
            case "8": //modifica testo di un post
                if(isAdmin()) {
                    $idPost = (int) $_GET["idPost"];
                    $data = "nuovo testo del post".$dbh->updateTextOfPost($idPost, htmlspecialchars($_POST["text"]));
                }
                break;
            case "9": //rimuove immagine di un post
                $idPost = (int) $_GET["idPost"];
                $imgPostPath = $dbh->getPathOfPost($idPost);
                if($imgPostPath != ""){
                    $percorsoFilePost = MEDIA_DIR.$imgPostPath;
                    if(file_exists($percorsoFilePost)){
                        unlink($percorsoFilePost);
                        $data = $dbh->removePathOfPost($idPost);
                    }
                }
                break;
        }
    }
}

header("Content-Type: application/json");
echo json_encode($data);

?>
