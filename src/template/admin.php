<?php
require_once "utils/bootstrap.php";
if (!isAdmin()) {
    header("Location: ../login.php");
}

// defensive session email
$session_email = isset($_SESSION["email"]) ? $_SESSION["email"] : "";

function generateAdmin(){
    global $dbh, $session_email;
    $html = "";
    $admins = $dbh->getAllAdminExcept($session_email);
    foreach ($admins as $amministratore) {
        $html .= '
        <li>
            <ul>
                <li><p>E-mail</p><p>' . $amministratore["email"] . '</p></li>
                <li><p>Nome utente</p><p>' . $amministratore["nome_utente"] . '</p></li>
                <li><button data-active="' . $amministratore["attivo"] . '"></button></li>
            </ul>
        </li>';
    }
    return $html;
}

function generateCourseOption() {
    global $dbh;
    $html = "";
    $courses = $dbh->getAllCourses();
        foreach ($courses as $corso) {
            $html .='
            <option value="' . $corso["corso_id"] .'">' . $corso["corso_nome"] . '</option>';
    }
    return $html;
}

function generateYears($option = true) {
    global $dbh;
    $html = "";
    $years = $dbh->getAllYears();
    foreach ($years as $year) {
        if ($option) {
            $html .='
            <option value="' . $year["years"] .'">' . $year["years"] . '</option>';
        } else {
            $html .= '<li>' . $year["years"] . '</li>';
        }
    }
    return $html;
}

function generateProfessors($option = true) {
    global $dbh;
    $html = "";
    $professors = $dbh->getAllProfessors();
    foreach ($professors as $professore) {
        if ($option) {
            $html .='
            <option value="' . $professore["email"] .'">' . $professore["nome"] . '</option>';
        } else {
            $html .= '<li>
            <p>E-mail</p><p>' . $professore["email"] . '</p>
            <p>Nome</p><p>' . $professore["nome"] . '
            </li>';
        }
    }
    return $html;
}

function generateAssistantOption($option = true) {
    global $dbh;
    $html = "";
    $assistants = $dbh->getAllAssistants();
    foreach ($assistants as $assistente) {
        if ($option) {
            $html .='
            <option value="' . $assistente["email"] .'">' . $assistente["nome"] . '</option>';
        } else {
            $html .= '<li>
            <p>E-mail</p><p>' . $assistente["email"] . '</p>
            <p>Nome</p><p>' . $assistente["nome"] . '
            </li>';
        }
    }
    return $html;
}


?>

<nav>
    <ul>
        <li id="classNav"><a href="#class">Classi e corsi</a></li>
        <li id="yearNav"><a href="#year">Anni</a></li>
        <li id="professorNav"><a href="#professor">Professori</a></li>
        <li id="assistantNav"><a href="#assistant">Assistenti</a></li>
        <li id="clientNav"><a href="#client">Clienti</a></li>
        <li id="adminNav"><a href="#admin">Amministratori</a></li>
    </ul>
</nav>
<section id="classes" >
    <ul>
        <li>
            <a href="courses.php">Elenco corsi</a>
            <form action="api/api-admin.php?action=addCourse" method="post">
                <fieldset>
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" />
                </fieldset>
                <input type="submit" value="Aggiungi corso" />
            </form>
        </li>
        <li>
            <a href="classes.php">Elenco classi</a>
            <form action="api/api-admin.php?action=addClass" method="post">
                <fieldset>
                    <label for="course">Corso</label>
                    <select name="corso" id="corso" required>
                        <option value="">-- Seleziona un corso --</option>
                        <?php echo generateCourseOption(); ?>
                    </select>
                    <label for="class">Classe</label>
                    <input list="classList" id="class" name="nome" placeholder="Aggiungi una classe" required />
                    <datalist id="classList">
                    </datalist>
                    <label for="yearSelect">Anno</label>
                    <select id="yearSelect" name="anno" required>
                        <option value="">-- Seleziona l'anno scolastico --</option>
                        <?php echo generateYears(); ?>
                    </select>
                    <label for="section">Sezione</label>
                    <input type="text" name="sezione" id="section" pattern="[A-Z]" placeholder="Aggiungi una nuova sezione" />
                    <label for="professor">Professore</label>
                    <select name="professore" id="professor" required>
                        <option value="">-- Seleziona un professore --</option>
                        <?php echo generateProfessors(); ?>
                    </select>
                    <label for="assistant">Assistente</label>
                    <select id="assistant" name="assistente" >
                        <option value="">-- Seleziona un assistente (opzionale) --</option>
                        <?php echo generateAssistantOption(); ?>
                    </select>
                </fieldset>
                <input type="submit" value="Aggiungi classe" />
            </form>
        </li>
    </ul>
</section>
<section id="years" hidden>
    <form action="api/api-admin.php?action=addYear" method="post">
        <fieldset>
            <label for="anno">Anno</label>
            <input type="text" pattern="[0-9]{4}-[0-9]{4}" id="anno" name="anno" placeholder="2025-2026" />
        </fieldset>
        <input type="submit" value="Aggiungi anno" />
    </form>
    <ul>
        <?php echo generateYears(false); ?>
    </ul>
</section>
<section id="professors" hidden>
                <form action="api/api-admin.php?action=addProfessor" method="post">
            <fieldset>
                <label for="pemail">E-mail</label>
                <input type="email" id="pemail" name="email" required placeholder="Inserisci un'email" />
                <label for="pname">Nome</label>
                <input type="text" id="pname" name="nome" required placeholder="Inserisci un nome" />
            </fieldset>
            <input type="submit" value="Aggiungi professore" />
        </form>

    <ul>
        <?php echo generateProfessors(false); ?>
    </ul>
</section>
<section id="assistants" hidden>
        <form action="api/api-admin.php?action=addAssistant" method="post">
    <fieldset>
        <label for="aemail">E-mail</label>
        <input type="email" id="aemail" name="email" required placeholder="Inserisci un'email" />
        <label for="aname">Nome</label>
        <input type="text" id="aname" name="nome" required placeholder="Inserisci un nome" />
    </fieldset>
    <input type="submit" value="Aggiungi assistente" />
    </form>
    <ul>
        <?php echo generateAssistantOption(false); ?>
    </ul>
</section>
<section id="clients" hidden>
    <ul>
    </ul>
</section>
<section id="admins" >
            <form action="api/api-admin.php?action=addAdmin" method="post">
        <fieldset>
            <label for="demail">E-mail</label>
            <input type="email" id="demail" name="email" placeholder="Aggiungi un'email" />
            <label for="dpassword">Password</label>
            <input type="password" id="dpassword" name="password" placeholder="Aggiungi una password" />
            <label for="dusername">Nome utente</label>
            <input type="text" id="dusername" name="nome_utente" placeholder="Inserisci nome utente" />
        </fieldset>
        <input type="submit" value="Aggiungi amministratore" />
    </form>
    <ul><?php echo generateAdmin(); ?>
    </ul>
</section>
