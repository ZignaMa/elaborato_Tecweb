<?php

require_once "utils/bootstrap.php";

if (!isUserLoggedIn()) {
    header("Location: login.php");
}

if (isAdmin()) {
    $userClasses = [];
} else {
        $userClasses = array_column(
        $dbh->getUserClassesByEmail($_SESSION["email"]),
        "classe_id"
    );

    if (isset($_POST["submit"]) && $_POST["submit"] == "Aggiorna") {
        $current = array_map(
            function ($val): int {
                return intval(str_replace("classe_", "", $val));
            },
            array_filter(
                array_keys($_POST),
                function ($val): bool {
                    return str_starts_with($val, "classe_");
                }
            )
        );

        $added = array_diff($current, $userClasses);
        $removed = array_diff($userClasses, $current);

        if (count($added) == 0) {
            $templateParams["message"] = "No class added. ";
        } else {
            foreach ($added as $elemento) {
                $dbh->userStartFollowClass($_SESSION["email"], $elemento);
            }
            $templateParams["message"] = "Added " . (string) count($added) . " classes. ";
        }

        if (count($removed) == 0) {
            $templateParams["message"] .= "No class removed.";
        } else {
            foreach ($removed as $elemento) {
                $dbh->userEndFollowClass($_SESSION["email"], $elemento);
            }
            $templateParams["message"] .= "Removed " . (string) count($removed) . " classes.";
        }

        // Refresh variable
        $userClasses = array_column(
            $dbh->getUserClassesByEmail($_SESSION["email"]),
            "classe_id"
        );
    }
}

$courses = []; // Used in template/classes.php
foreach ($dbh->getAllCourses(isAdmin() ? 0 : $_SESSION["email"]) as $corso) {
    $ris = $dbh->getClassesByCourseID($corso["corso_id"]);
    $temporaneo = [];
    foreach ($ris as $elemento) {
        if (in_array($elemento["id"], $userClasses)) {
            $elemento["checked"] = true;
        } else {
            $elemento["checked"] = false;
        }
        // Usare i campi in italiano: 'nome', 'sezione', 'anno_accademico'
    $temporaneo[$elemento["anno_accademico"]][] = $elemento;
    }
    $courses[$corso["corso_nome"]] = $temporaneo;
}

    $templateParams["title"] = "Centro studio - Classi";
$templateParams["main"] = "template/classes.php";
$templateParams["css"] = ["css/classes.css"];

require "template/skeleton.php";

?>
