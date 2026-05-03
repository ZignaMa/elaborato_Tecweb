        <h2>Crea nuovo post</h2>
        <form action="api/api-posts.php?action=5" method="POST" enctype="multipart/form-data">
            <?php if(isset($templateParams["errorelogin"])): ?>
            <p><?php echo $templateParams["errorelogin"]; ?></p>
            <?php endif; ?>
            <ul>
                <li>
                    <label for="corso">Corso:</label>
                    <select id="corso" name="corso" required></select>
                </li><li>
                    <label for="classe">Materia:</label>
                    <select id="classe" name="classe" required><option value="">-- Settare la scelta precedente --</option></select>
                </li><li>
                    <label for="anno">Anno:</label>
                    <select id="anno" name="anno" required><option value="">-- Settare la scelta precedente --</option></select>
                </li><li>
                    <label for="sezione">Classe:</label>
                    <select id="sezione" name="sezione" required><option value="">-- Settare la scelta precedente --</option></select>
                </li><li>
                    <label for="text">Scrivi il post</label><textarea id="text" name="text" required maxlength="65500" minlength="1"></textarea>
                </li><li>
                    <label for="imgpost">Seleziona immagine</label>
                    <input type="file" name="imgpost" id="imgpost" hidden/>
                    <span>Nessuna immagine selezionata</span>
                </li><li>
                    <input type="submit" name="submit" value="Aggiungi post" />
                </li>
            </ul>
        </form>