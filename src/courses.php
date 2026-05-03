<?php

require_once "utils/bootstrap.php";

if (!isUserLoggedIn()) {
    header("Location: login.php");
}

if (isAdmin()) {
    $userCourses = [];
} else {
    $userCourses = array_column(
        $dbh->getAllCourses($_SESSION["email"]),
        "corso_id"
    );

    if (isset($_POST["submit"]) && $_POST["submit"] == "Aggiorna") {
        $current = array_map(
            function ($val): int {
                return intval(str_replace("corso_", "", $val));
            },
            array_filter(
                array_keys($_POST),
                function ($val): bool {
                    return str_starts_with($val, "corso_");
                }
            )
        );

        $added = array_diff($current, $userCourses);
        $removed = array_diff($userCourses, $current);

        if (count($added) == 0) {
            $templateParams["message"] = "No course added. ";
        } else {
                foreach ($added as $elemento) {
                $dbh->addCourseToUser($_SESSION["email"], $elemento);
            }
            $templateParams["message"] = "Added " . (string) count($added) . " courses. ";
        }

        if (count($removed) == 0) {
            $templateParams["message"] .= "No course removed.";
        } else {
            foreach ($removed as $elemento) {
                $dbh->removeCourseForUser($_SESSION["email"], $elemento);
            }
            $templateParams["message"] .= "Removed " . (string) count($removed) . " courses.";
        }

        // Refresh variable
            $userCourses = array_column(
                $dbh->getAllCourses($_SESSION["email"]),
                "corso_id"
            );
    }
}

$courses = []; // Used in template/courses.php
foreach ($dbh->getAllCourses() as $corso) {
    // Usare solo campi in italiano: 'corso_id' e 'corso_nome'
    $corso["checked"] = in_array($corso["corso_id"], $userCourses);
    $courses[$corso["corso_nome"]] = $corso;
}

    $templateParams["title"] = "Centro studio - Corsi";
$templateParams["main"] = "template/courses.php";
$templateParams["css"] = ["css/courses.css"];

require "template/skeleton.php";

?>
