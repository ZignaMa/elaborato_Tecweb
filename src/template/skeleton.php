<!DOCTYPE html>
<html lang="it">
<?php
// Ensure $templateParams is defined to avoid warnings when templates include this skeleton
if (!isset($templateParams) || !is_array($templateParams)) {
    $templateParams = [];
}
?>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($templateParams["title"]) ? $templateParams["title"] : ""; ?></title>
    <link rel="stylesheet" type="text/css" href="./css/common.css" />
    <?php
    if (isset($templateParams["css"])):
        foreach($templateParams["css"] as $foglioStile):
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $foglioStile; ?>" />
    <?php
        endforeach;
    endif;
    ?>
</head>
<?php
// se è impostato il flag per nascondere il brand footer, aggiungi una classe al body
$bodyClass = '';

$hideBrand = false;
if (isset($templateParams['layout']) && is_array($templateParams['layout']) && isset($templateParams['layout']['hide_footer_brand'])) {
    $hideBrand = (bool)$templateParams['layout']['hide_footer_brand'];
} elseif (isset($templateParams['hide_footer_brand'])) {
    $hideBrand = (bool)$templateParams['hide_footer_brand'];
}
if ($hideBrand) {
    $bodyClass = ' class="hide-brand-footer"';
}
?>
<body<?php echo $bodyClass; ?>>
    <header>
        <nav>
            <ul>
                <li hidden><a href="help.php">Guida</a></li><?php
                    if (!isUserLoggedIn()):
                ?><li><a href="login.php">Accedi</a></li><li class="brand-button"><a class="brand-text" href="register.php">Registrati</a></li><?php
                    else:
                        if (isAdmin()):
                ?><li class="brand-button"><a href="admin.php" class="brand-text"><?php
                            $img_src = isset($_SESSION["img_profilo"]) && !empty($_SESSION["img_profilo"]) ? "uploads/media/" . $_SESSION["img_profilo"] : "uploads/static/icons/user.svg";
                            echo '<img src="' . htmlspecialchars($img_src, ENT_QUOTES) . '" alt="Foto profilo utente" />' . htmlspecialchars($_SESSION["nome_utente"]);
                        ?></a></li>
                            <li><a class="logout-text" href="logout.php">Esci<img src="./uploads/static/icons/arrow-right-from-bracket.svg" alt="Icona esci" /></a></li><?php
                        elseif(!isset($templateParams["in_user_page"]) || !$templateParams["in_user_page"]):
                ?><li class="brand-button"><p class="brand-text"><?php
                        $img_src = isset($_SESSION["img_profilo"]) && !empty($_SESSION["img_profilo"]) ? "uploads/media/" . $_SESSION["img_profilo"] : "uploads/static/icons/user.svg";
                        echo '<img src="' . htmlspecialchars($img_src, ENT_QUOTES) . '" alt="Foto profilo utente" />' . htmlspecialchars($_SESSION["nome_utente"]) . '<img src="./uploads/static/icons/chevron-right.svg" alt="Icona menu" />';
                        ?></p></li><?php
                        else:
                ?><li><a class="logout-text" href="logout.php">Esci<img src="./uploads/static/icons/arrow-right-from-bracket.svg" alt="Icona esci" /></a></li><?php
                        endif;
                    endif;
                ?>
            </ul>
            <?php if (isUserLoggedIn() && (!isset($templateParams["in_user_page"]) || !$templateParams["in_user_page"]) && !isAdmin()): ?>
            <div>
                <ul>
                    <li><a href="feed.php">Bacheca</a></li><li><a href="user.php">Il mio profilo</a></li><li><a href="user.php#classes">Le mie classi</a></li><li><a href="user.php#posts">I miei post</a></li><li><a href="logout.php">Esci</a></li>
                </ul>
            </div>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?php
        if (isset($templateParams["main"])) {
            require($templateParams["main"]);
        }
        ?>
    </main>
    <?php if (!$hideBrand): ?>
    <footer>
    <a class="brand-link" href="index.php">
        <h1 class="brand-title">Centro studio</h1>
        <img class="brand-logo" src="./uploads/static/centro-studio.png" alt="Centro studio logo" />
    </a>
    <p>Centro studio - &copy; 2026</p>
    </footer>
    <?php endif; ?>
    <script src="./js/common.js"></script>
    <?php
    if (isset($templateParams["js"])):
        foreach($templateParams["js"] as $scriptJS):
    ?>
    <script src="<?php echo $scriptJS; ?>"></script>
    <?php
        endforeach;
    endif;
    ?>
</body>
</html>
