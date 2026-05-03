<article>
    <header>
    </header>
    <section>
    </section>
</article>
<section>
    <form action="#" method="POST" enctype="multipart/form-data">
        <?php if(isset($templateParams["errorelogin"])): ?>
        <p><?php echo $templateParams["errorelogin"]; ?></p>
        <?php endif; ?>
        <ul>
            <li><textarea id="text" name="text" required maxlength="65500" minlength="1" placeholder="Aggiungi commento..."></textarea>
            </li>
            <li>
                <input type="file" name="imgcomment" id="imgcomment" hidden/>
                <label for="imgcomment"><img src="uploads/static/icons/photo.svg" alt="Carica immagine" /></label>
                <span id="file-name"></span>
            </li>
            <li>
                <input type="submit" name="submit" id="sendcomment" value="Aggiungi commento" hidden /><label for="sendcomment"><img src="uploads/static/icons/arrow-up-send.svg" alt="Invia commento" /></label>
            </li>
        </ul>
    </form>
    <ul>
    </ul>
    <img src="#" alt="immagine cliccata ingrandita" hidden/>
</section>