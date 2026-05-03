<?php
// Defensive defaults to avoid undefined index warnings when template is rendered without expected data
$templateParams = isset($templateParams) && is_array($templateParams) ? $templateParams : [];
$courses = isset($courses) && is_array($courses) ? $courses : [];
?>
        <form action="<?php echo isAdmin() ? "#" : "classes.php"; ?>" method="POST">
            <?php if(isset($templateParams["message"])): ?>
            <p><?php echo $templateParams["message"]; ?></p>
            <?php endif; ?>
            <?php if (count($courses) == 0): ?>
            Non sei iscritto a nessun corso.
            <a href="courses.php">Clicca qui per iscriverti a un corso</a>
            <?php endif; ?>
            <?php foreach($courses as $corso => $years): ?>
                <h2><?php echo htmlspecialchars($corso); ?></h2>
                <?php krsort($years); foreach($years as $year => $classes): ?>
                <fieldset>
                    <legend><?php echo htmlspecialchars($year); ?></legend>
                    <ul>
                    <?php foreach($classes as $classe):
                        // normalize classe data
                        $classe = is_array($classe) ? $classe : [];
                        $cid = isset($classe["id"]) ? $classe["id"] : '';
                        $cnome = isset($classe["nome"]) ? $classe["nome"] : '';
                        $csezione = isset($classe["sezione"]) ? $classe["sezione"] : '';
                        $checked = !empty($classe["checked"]);
                    ?>
                    <li>
                        <label <?php if (!isAdmin()) echo "for=\"classe_" . htmlspecialchars($cid) . "\""; ?>>
                                <?php if (isAdmin()): ?>
                                    <a href="class.php?classe_id=<?php echo urlencode($cid);
                                    ?>"><?php echo htmlspecialchars($cnome); ?> | <?php echo htmlspecialchars($csezione); ?></a>
                            <?php else: ?>
                                <input type="checkbox" id="classe_<?php echo htmlspecialchars($cid);
                                ?>" name="classe_<?php echo htmlspecialchars($cid); ?>" value="1" <?php
                                echo $checked ? "checked" : "";
                                ?> /><?php
                                echo htmlspecialchars($cnome); ?> | <?php echo htmlspecialchars($csezione);
                            endif; ?>
                        </label>
                    </li>
                    <?php endforeach; ?>
                    </ul>
                </fieldset>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php if (!isAdmin() && count($courses) > 0): ?>
            <div>
                <input type="submit" name="submit" value="Aggiorna" />
            </div>
            <?php endif; ?>
        </form>
