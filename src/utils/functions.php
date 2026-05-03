<?php
function isActive($pagename){
    if(basename($_SERVER['PHP_SELF'])==$pagename){
        echo " class='active' ";
    }
}

function logout() {
    session_unset();
    session_destroy();
}

function getIdFromName($nome){
    return preg_replace("/[^a-z]/", '', strtolower($nome));
}

function isUserLoggedIn(){
    return !empty($_SESSION['email']);
}

function isAdmin(){
    return isset($_SESSION["amministratore"]) && $_SESSION["amministratore"] === true;
}

function extractId($string){
    if (preg_match('/_(\d+)$/', $string, $matches)) {
        return (int)$matches[1];
    }
    return null;
}

function createPathFromComment($idPost, $dbh, $imgName){
    $maxImgInPost = 100000;
    $path = $dbh->getCommentPathViaPostID($idPost);
    $ext = "." . strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
    for($i = 0; $i < $maxImgInPost; $i++) {
        $newPath = $path.$i.$ext;
        if($dbh->isFreeCommentPath($newPath)){
            return $newPath;
        }
    }
    return null;
}

function getFileNameFromPath($path){
    $nomeFile = basename($path);
    return $nomeFile;
}

function registerLoggedUser($user) {
    $_SESSION["email"] = $user["email"];
    $_SESSION["nome_utente"] = $user["nome_utente"];
    $_SESSION["amministratore"] = isset($user["amministratore"]) && $user["amministratore"] === 1;
    if (isset($user["img_profilo"])) {
        $_SESSION["img_profilo"] = $user["img_profilo"];
    }
}

function getEmptyArticle(){
    return array("idarticolo" => "", "titoloarticolo" => "", "imgarticolo" => "", "testoarticolo" => "", "anteprimaarticolo" => "", "categorie" => array());
}

function getAction($action){
    $result = "";
    switch($action){
        case 1:
            $result = "Inserisci";
            break;
        case 2:
            $result = "Modifica";
            break;
        case 3:
            $result = "Cancella";
            break;
    }

    return $result;
}

// Handle file upload
// $fileRisorsa: must be `$_FILES["input_id"]` with the proper `input_id`
// $targetPath: must be an existent directory where the file should be saved
// $nomeFile: if not null this will be the name of the file, else will be used `$fileRisorsa["name"]`
function uploadDocument(
    array $fileRisorsa,
    string $targetPath,
    string $nomeFile = null,
): array {
    $allowedTypes = ["image/jpeg", "image/png", "image/gif", "application/pdf"];

    $allowedExtensions = ["jpg", "jpeg", "png", "gif", "pdf"];

    $maxFileSize = 4 * 1024 * 1024; // 4MiB

    if (!isset($fileRisorsa) || $fileRisorsa["error"] !== UPLOAD_ERR_OK) {
        return ["error" => "Invalid file upload"];
    }

    if ($fileRisorsa["size"] > $maxFileSize) {
        return ["error" => "File too large. Maximum size is 4MiB"];
    }

    if (!in_array(mime_content_type($fileRisorsa["tmp_name"]), $allowedTypes)) {
        return ["error" => "Invalid file type"];
    }

    if (
        !in_array(
            strtolower(pathinfo($fileRisorsa["name"], PATHINFO_EXTENSION)),
            $allowedExtensions,
        )
    ) {
        return ["error" => "Invalid file extension"];
    }

    if (!is_dir($targetPath) || !is_writable($targetPath)) {
        return ["error" => "Upload directory is not accessible"];
    }

    $dest =
        $targetPath .
        ($nomeFile !== null ? $nomeFile : htmlspecialchars($fileRisorsa["name"]));
    if (!move_uploaded_file($fileRisorsa["tmp_name"], $dest)) {
        return ["error" => "File upload failed"];
    }
    return ["success" => true];
}

?>
