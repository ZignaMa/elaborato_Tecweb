<?php

require_once "utils/bootstrap.php";

if(isUserLoggedIn()) {
    $templateParams["js"] = array();
    $templateParams["css"] = array();
    if(!isset($_GET["idpost"])) {
    $templateParams["title"] = "Centro studio - Nuovo post";
        $templateParams["main"] = "template/create-post-form.php";
        array_push($templateParams["css"], "css/post-insert.css");
        array_push($templateParams["js"], "js/create-post.js");
    } else {
        if(isAdmin()) {
            array_push($templateParams["css"], "css/single-post-admin.css");
        } else {
            array_push($templateParams["css"], "css/post-single.css");
        }
    $templateParams["title"] = "Centro studio - Post";
        // Nascondi il brand footer nella vista singolo post (usare namespace layout)
        if (!isset($templateParams['layout']) || !is_array($templateParams['layout'])) {
            $templateParams['layout'] = array();
        }
        $templateParams['layout']['hide_footer_brand'] = true;
        $templateParams["main"] = "template/single-post.php";
        array_push($templateParams["js"], "js/single-post.js");
    }
} else {
    header("Location: login.php");
}

require 'template/skeleton.php';
?>