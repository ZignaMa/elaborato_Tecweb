<?php

require_once "../utils/bootstrap.php";

function return400(string $error): void {
    http_response_code(400);
    header("Content-Type: application/json");
    echo json_encode([
        "error" => $error,
    ]);
    exit();
}

if (!isUserLoggedIn()) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode([
        "error" => "Error: you are unauthenticated",
    ]);
    exit();
}

$data = [];

/**
 * Return the requested email from request parameters.
 * Use `email`. If none provided, returns session email.
 * On invalid email returns boolean false.
 */
function getRequestedEmail() {
    if (isset($_REQUEST["email"]) && $_REQUEST["email"] != "null") {
        $email = filter_var($_REQUEST["email"], FILTER_SANITIZE_EMAIL);
        return $email === false ? false : $email;
    }
    return $_SESSION["email"];
}

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "update":
            if (!isset($_FILES["img_profilo"])) {
                return400("No file uploaded");
            }
            $nomeFile = $_SESSION["email"] . "." . strtolower(pathinfo($_FILES["img_profilo"]["name"], PATHINFO_EXTENSION));
            $dest = MEDIA_DIR . "/profiles/";
            if (!is_dir($dest)) {
                mkdir($dest, 0700, true);
            }
            $res = uploadDocument($_FILES["img_profilo"], $dest, $nomeFile);
            if (isset($res["error"])) {
                return400($res["error"]);
            } else {
                $dbh->updateUserProfileImg($_SESSION["email"], "/profiles/" . $nomeFile);
                $data = ["message" => "File uploaded successfully"];
            }
            break;
        case "get_posts":
            $email = getRequestedEmail();
            if ($email === false) return400("Invalid email");
            if (isset($_REQUEST["count"]) && $_REQUEST["count"] == 0) {
                $data["message"] = $dbh->getUserPostsCountByEmail($email);
            } else {
                if (!isset($_REQUEST["page_number"]) || !isset($_REQUEST["items_count"])) {
                    return400("Missing parameters");
                }
                $page_number = filter_var($_REQUEST["page_number"], FILTER_VALIDATE_INT, [
                    "options" => [
                        "default" => 0,
                        "min_range" => 0,
                    ]
                ]);
                if ($page_number === false) return400("Invalid page_number");
                $items_count = filter_var($_REQUEST["items_count"], FILTER_VALIDATE_INT, [
                    "options" => [
                        "default" => 10,
                        "min_range" => 1,
                        "max_range" => 100
                    ]
                ]);
                if ($items_count === false) return400("Invalid items_count");
                $data["message"] = $dbh->getUserPostsByEmail(
                    $email,
                    $page_number,
                    $items_count
                );
            }
            break;
        case "get_comments":
            $email = getRequestedEmail();
            if ($email === false) return400("Invalid email");
            if (isset($_REQUEST["count"]) && $_REQUEST["count"] == 0) {
                $data["message"] = $dbh->getUserCommentsCountByEmail($email);
            } else {
                if (!isset($_REQUEST["page_number"]) || !isset($_REQUEST["items_count"])) {
                    return400("Missing parameters");
                }
                $page_number = filter_var($_REQUEST["page_number"], FILTER_VALIDATE_INT, [
                    "options" => [
                        "default" => 0,
                        "min_range" => 0,
                    ]
                ]);
                if ($page_number === false) return400("Invalid page_number");
                $items_count = filter_var($_REQUEST["items_count"], FILTER_VALIDATE_INT, [
                    "options" => [
                        "default" => 10,
                        "min_range" => 1,
                        "max_range" => 100
                    ]
                ]);
                if ($items_count === false) return400("Invalid items_count");
                $data["message"] = $dbh->getUserCommentsByEmail(
                    $email,
                    $page_number,
                    $items_count
                );
            }
            break;
        case "get_classes":
            $email = getRequestedEmail();
            if ($email === false) return400("Invalid email");
            if (isset($_REQUEST["count"]) && $_REQUEST["count"] == 0) {
                $data["message"] = $dbh->getUserClassesCountByEmail($email);
            } else {
                $ris = $dbh->getUserClassesByEmail($email); // No pagination
                // Groups classes by course_name and school_year
                $classes = [];
                foreach($ris as $elemento) {
                    $classes[$elemento["corso_nome"]][] = $elemento;
                }
                $data["message"] = $classes;
            }
            break;
        case "toggle_active":
            if (!isAdmin()) {
                http_response_code(403);
                header("Content-Type: application/json");
                echo json_encode(["error" => "Error: permission denied"]);
                exit();
            }
            // require canonical `email` param
            if (!isset($_REQUEST["email"]) || $_REQUEST["email"] == "null") {
                return400("Missing parameters");
            }
            $email = getRequestedEmail();
            if ($email === false) return400("Invalid email");
            $dbh->changeActiveStatus($email);
            $data["message"] = "User changed successfully";
            break;
        default:
            return400("Invalid action");
    }
} else {
    return400("Unspecified action");
}

header("Content-Type: application/json");
echo json_encode($data);

?>
