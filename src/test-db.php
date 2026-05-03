<?php

require_once(__DIR__ . '/utils/bootstrap.php');

if (isset($dbh)) {
    echo '<h3>Verifica connessione DB</h3>';
    echo '<pre>' . htmlspecialchars($dbh->presentation()) . '</pre>';
} else {
    echo '<h3>Impossibile trovare $dbh</h3>';
    echo '<p>Controlla che <code>utils/bootstrap.php</code> sia presente e correttamente configurato.</p>';
}

?>
