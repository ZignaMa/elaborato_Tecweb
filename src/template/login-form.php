        <form action="#" method="POST">
            <?php if(isset($templateParams["error"])): ?>
            <p><?php echo $templateParams["error"]; ?></p>
            <?php endif; ?>
            <img src="./uploads/static/centro-studio.png" alt="" />
            <ul>
                <li>
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required/>
                </li>
                <li>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required/>
                </li>
                <li>
                    <input type="submit" name="submit" value="Accedi" />
                </li>
            </ul>
        </form>
