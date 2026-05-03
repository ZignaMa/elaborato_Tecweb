        <form action="#" method="POST">
            <?php if(isset($templateParams["error"])): ?>
            <p><?php echo $templateParams["error"]; ?></p>
            <?php endif; ?>
            <img src="./uploads/static/centro-studio.png" alt="" />
            <ul>
                <li>
                    <label for="nome_utente">Nome utente:</label><input type="text" id="nome_utente" name="nome_utente" required/>
                </li>
                <li>
                    <label for="email">E-mail:</label><input type="email" id="email" name="email" required/>
                </li>
                <li>
                    <label for="password">Password:</label><input type="password" id="password" name="password" required/>
                </li>
                <li>
                    <label for="corso">Corso seguito:</label>
                    <select id="corso" name="corso" required>
                    </select>
                </li>
                <li>
                    <input type="submit" name="submit" value="Registrati" />
                </li>
            </ul>
        </form>
