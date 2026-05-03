<?php
// Defensive defaults
$templateParams = isset($templateParams) && is_array($templateParams) ? $templateParams : [];
$courses = isset($courses) && is_array($courses) ? $courses : [];
?>
        <form action="<?php echo isAdmin() ? "#" : "courses.php"; ?>" method="POST">
            <?php if(isset($templateParams["message"])): ?>
            <p><?php echo $templateParams["message"]; ?></p>
            <?php endif; ?>
            <ul>
            <?php foreach($courses as $corso):
                $corso = is_array($corso) ? $corso : [];
                $cid = isset($corso["corso_id"]) ? $corso["corso_id"] : '';
                $cname = isset($corso["corso_nome"]) ? $corso["corso_nome"] : '';
                $checked = !empty($corso["checked"]);
            ?>
                <li>
                    <label <?php if (!isAdmin()) echo "for=\"corso_" . htmlspecialchars($cid) . "\""; ?> >
                        <?php if (!isAdmin()): ?>
                        <input type="checkbox" id="corso_<?php echo htmlspecialchars($cid);
                        ?>" name="corso_<?php echo htmlspecialchars($cid); ?>" value="1" <?php
                        echo $checked ? "checked" : "";
                        ?> /><?php
                        endif;
                        echo htmlspecialchars($cname); ?>
                    </label>
                </li>
            <?php endforeach; ?>
            </ul>
            <?php if (!isAdmin()): ?>
            <div>
                <input type="submit" name="submit" value="Aggiorna" />
            </div>
            <?php endif; ?>
        </form>
