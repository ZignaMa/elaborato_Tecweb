        <?php
        // Normalize $templateParams["user"] to avoid undefined index warnings
        $user = isset($templateParams["user"]) && is_array($templateParams["user"]) ? $templateParams["user"] : [];
        $user_email = isset($user["email"]) ? $user["email"] : '';
        $user_nome = isset($user["nome_utente"]) ? $user["nome_utente"] : '';
        $user_img = isset($user["img_profilo"]) ? $user["img_profilo"] : null;
        ?>

        <article>
            <header>
                <img src="<?php
                if ($user_img !== null) {
                    echo "uploads/media/" . $user_img;
                } else {
                    echo "uploads/static/icons/user.svg";
                }?>" alt="" /><p><?php echo $user_nome; ?></p>
                <?php
                if ($user_email !== '' && $user_email === (isset($_SESSION["email"]) ? $_SESSION["email"] : null)):
                ?><a href="feed.php">Bacheca</a><?php endif; ?>
            </header>
            <section>
                <nav>
                    <ul>
                        <li><a href="#info">Info</a></li><li><a href="#posts">Post</a></li><li><a href="#comments">Commenti</a></li><li><a href="#classes">Classi</a></li>
                    </ul>
                </nav>
                <div id="info" hidden>
                    <form action="#" method="POST" enctype="multipart/form-data">
                        <ul>
                            <li>
                                <label for="email">E-mail:</label>
                                <input disabled type="text" id="email" name="email" value="<?php echo $user_email;?>" />
                            </li>
                            <li>
                                <label for="nome_utente">Nome utente:</label>
                                <input disabled type="text" id="nome_utente" name="nome_utente" value="<?php echo $user_nome;?>" />
                            </li>
                            <li>
                                <label <?php
                                    if($user_email !== '' && $user_email === (isset($_SESSION["email"]) ? $_SESSION["email"] : null)) echo 'for="img_profilo"' ;
                                ?>>Immagine profilo:</label>
                                <input type="hidden" name="MAX_FILE_SIZE" value="400000" />
                                <?php
                                if ($user_img !== null) {
                                    echo "<img src=\"uploads/media/" . $user_img . "\" alt=\"immagine profilo\" />";
                                } else {
                                    echo "Nessuno";
                                }
                                if ($user_email !== '' && $user_email === (isset($_SESSION["email"]) ? $_SESSION["email"] : null)) {
                                    echo " <input type=\"file\" name=\"img_profilo\" id=\"img_profilo\" accept=\"image/*\" required />";
                                }
                                ?>
                            </li>
                            <?php
                            if ($user_email !== '' && $user_email === (isset($_SESSION["email"]) ? $_SESSION["email"] : null)): ?><li>
                                <input type="submit" value="Aggiorna" />
                            </li><?php
                            elseif (isAdmin() && ($user_email === '' || $user_email !== (isset($_SESSION["email"]) ? $_SESSION["email"] : null))):
                            ?><li>Stato: <?php
                                $res = $dbh->checkIsActive($user_email)[0];
                                echo ($res["attivo"] === 1) ? "attivo" : "bloccato";
                                ?> <input type="submit" value="<?php echo ($res["attivo"]) ? "Blocca" : "Attiva"; ?>" /></li>
                            <?php endif; ?>
                        </ul>
                    </form>
                </div>
                <div id="posts" hidden>
                </div>
                <div id="comments" hidden>
                </div>
                <div id="classes" hidden>
                </div>
            </section>
        </article>
