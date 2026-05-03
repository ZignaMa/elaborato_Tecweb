<?php
require_once 'utils/bootstrap.php';
$delimiter = " | ";
$classeId = isset($_GET['classe_id']) ? $_GET['classe_id'] : null;

if ($classeId !== null) {
    $information = $dbh->getProfessorNAssistantOfClass($classeId);
} else {
    $information = [];
}

if (empty($information) || !isset($information[0])) {
    $information = [[
        "corso_nome" => "",
        "corso_id" => "",
        "nome" => "",
        "sezione" => "",
        "anno_accademico" => "",
        "assistente_nome" => null,
        "assistente_email" => null,
        "professore_nome" => null,
        "professore_email" => null,
    ]];
}

$classe = $information[0]["corso_nome"] . $delimiter . $information[0]["nome"] .
    $delimiter . $information[0]["sezione"] . $delimiter . $information[0]["anno_accademico"];
$submitURL = "api/api-class-resources.php?corso_id=" . $information[0]["corso_id"] . "&classe_id=" . $classeId . "&sezione=" . $information[0]["sezione"] . "&anno=" . $information[0]["anno_accademico"];

$assistantUl = '
        <ul>
            <li>
                <h3>Assistente</h3>
                <p>' . $information[0]["assistente_nome"] . '</p>
            </li>
            <li>
                <h3>E-mail</h3>
                <p>' . $information[0]["assistente_email"] . '</p>
            </li>
        </ul>';
?>


<header>
    <h2><?php echo $classe ?></h2>
    <button id="followButton" hidden>Segui</button>
    <button id="unfollowButton" hidden>Smetti di seguire</button>
</header>
<section>
    <nav>
        <ul>
            <li id="postsNav"><a href="#">Post</a></li>
            <li id="infoNav"><a href="#">Info</a></li>
            <li id="resourcesNav"><a href="#">Risorse</a></li>
        </ul>
    </nav>
    <div id="posts" data-admin="<?php echo isAdmin()?>">
    </div>
    <div id="info">
        <ul>
            <li>
                <h3>Professore</h3>
                <p><?php echo $information[0]["professore_nome"] ?></p>
            </li>
            <li>
                <h3>E-mail</h3>
                <p><?php echo $information[0]["professore_email"] ?></p>
            </li>
        </ul>
        <?php
        if (!empty($information[0]["assistente_email"])) {
            echo $assistantUl;
        }
        ?>
    </div>
    <div id="resources" data-admin="<?php echo isAdmin()?>">
        <button>Aggiungi risorse</button>
        <form action=<?php echo $submitURL ?>>
            <p id="errors" hidden></p>
            <input type="file" name="newResources[]" id="newResources" multiple hidden/>
            <input type="submit" name="submit" id="submit" value="Conferma scelta" hidden/>
        </form>
        <ul>

        </ul>
    </div>
</section>
