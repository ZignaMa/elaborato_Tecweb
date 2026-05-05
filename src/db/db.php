<?php
class DatabaseHelper{
    private $db;

    public function __construct($servername, $username, $password, $dbname, $port){
        $this->db = new mysqli($servername, $username, $password, $dbname, $port);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function presentation() {
        if($this->db) {
            return "Connesso correttamente al database: " . $this->db->query("SELECT DATABASE()")->fetch_row()[0]."<br />";
        } else {
            return "Nessuna connessione al database";
        }
    }

    public function checkIsActive($id){
        $query = "SELECT attivo FROM utenti WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    public function checkLogin(string $email, string $password): array {
        $query = "SELECT email, nome_utente, amministratore, attivo, img_profilo, password FROM utenti WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $risultato = $stmt->get_result();
        $user = $risultato->fetch_all(MYSQLI_ASSOC);
        if (count($user) > 0 && password_verify($password, $user[0]["password"])) {
            unset($user[0]["password"]);
            return $user;
        }
        return [];
    }

    public function getAllCourses($email=0){
        if($email!=0){
            $query = "SELECT corsi_dei_clienti.corso_id AS corso_id, corsi.nome AS corso_nome FROM corsi, corsi_dei_clienti WHERE corsi_dei_clienti.email = ? AND corsi_dei_clienti.corso_id = corsi.id ORDER BY corso_nome ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $risultato = $stmt->get_result();
        } else {
            $query = "SELECT id AS corso_id, nome AS corso_nome FROM corsi ORDER BY corso_nome ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $risultato = $stmt->get_result();
        }
        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllClasses($email=0, $corso=0) {
        if($email!=0 && $corso!=0) {
            $corso_int = (int) $corso;
            $query = "SELECT DISTINCT classi.nome AS nome FROM classi JOIN classi_dei_clienti ON classi.id = classi_dei_clienti.classe_id WHERE classi_dei_clienti.email = ? AND classi.corso_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('si', $email, $corso_int);
            $stmt->execute();
            $risultato = $stmt->get_result();
            return $risultato->fetch_all(MYSQLI_ASSOC);
        } else if($email!=0 && $corso==0) {
            //prendere tutte le materie collegate all'utente
        } else if($email==0 && $corso!=0) {
            //prendere tutte le materie collegate al corso
        } else {
            //prendere tutte le materie disponibili nel db
        }
        return [];
    }

    public function addCourseToUser($email, $idCorso){
        $query = "INSERT INTO corsi_dei_clienti (email, corso_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $email, $idCorso);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function removeCourseForUser(string $email, int $course_id): bool {
        $query = "DELETE FROM corsi_dei_clienti WHERE email = ? AND corso_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $email, $course_id);

        return $stmt->execute();
    }

    public function addNewUser(string $email, string $password, string $nome_utente, int $idCorso = 0, int $admin = 0): array {
        $active = 1;
        $success["utente"] = false;
        $success["corso"] = false;
        $password_hash = password_hash($password, PASSWORD_ARGON2ID);
        $query = "INSERT INTO utenti (email, password, nome_utente, amministratore, attivo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssii', $email, $password_hash, $nome_utente, $admin, $active);
        $success["utente"] = $stmt->execute();
        if($success["utente"] && $admin == 0){
            $success["corso"] = $this->addCourseToUser($email, $idCorso);
        }
        $stmt->close();

        return $success;
    }

    public function getUserViaEmail(string $email): array {
        $query = "SELECT email, nome_utente, amministratore, attivo, img_profilo FROM utenti WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    // Update user profile picture.
    // Returns true on success
    public function updateUserProfileImg(string $email, string $path): bool {
        $query = "UPDATE utenti SET img_profilo = ? WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $path, $email);
        return $stmt->execute();
    }

    // Get the number of posts the user created.
    public function getUserPostsCountByEmail(string $email): int {
        $query = "SELECT COUNT(p.id) AS n FROM pubblicazioni p WHERE p.email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC)[0]["n"];
    }

    // Get posts the user has written.
    public function getUserPostsByEmail(string $email, int $pageNumber, int $postsPerPage): array {
        $query = "SELECT u.email AS email, cl.id AS classe_id, p.id AS pubblicazione_id, u.img_profilo AS img_profilo, " .
            "u.nome_utente AS nome_utente, co.nome AS corso_nome, cl.nome AS classe_nome, cl.sezione, " .
            "cl.anno_accademico AS anno_accademico, p.data_e_ora AS data_e_ora, p.testo AS testo, p.percorso AS percorso " .
            "FROM utenti u, pubblicazioni p, classi cl, corsi co " .
            "WHERE p.email = ? AND p.classe_id = cl.id AND p.email = u.email AND co.id = cl.corso_id " .
            "ORDER BY p.data_e_ora DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $offset = $pageNumber * $postsPerPage;
        $stmt->bind_param('sii', $email, $postsPerPage, $offset);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    // Get the number of comments the user created.
    public function getUserCommentsCountByEmail(string $email): int {
        $query = "SELECT COUNT(c.id) AS n FROM commenti c WHERE c.email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC)[0]["n"];
    }

    // Get comments the user has written.
    public function getUserCommentsByEmail(string $email, int $pageNumber, int $commentsPerPage): array {
        $query = "SELECT u.email AS email, cl.id AS classe_id, p.id AS pubblicazione_id, u.img_profilo AS img_profilo, " .
            "u.nome_utente AS nome_utente, co.nome AS corso_nome, cl.nome AS classe_nome, cl.sezione, " .
            "cl.anno_accademico AS anno_accademico, cm.data_e_ora AS data_e_ora, p.testo AS testo_pubblicazione, p.percorso AS percorso_pubblicazione, " .
            "cm.percorso AS percorso_commento, cm.testo AS testo_commento, cm.id AS commento_id " .
            "FROM utenti u, pubblicazioni p, classi cl, corsi co, commenti cm " .
            "WHERE cm.email = ? AND p.classe_id = cl.id AND p.email = u.email AND " .
            "co.id = cl.corso_id AND cm.pubblicazione_id = p.id " .
            "ORDER BY cm.data_e_ora DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $offset = $pageNumber * $commentsPerPage;
        $stmt->bind_param('sii', $email, $commentsPerPage, $offset);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    // Get the number of classes the user follows.
    public function getUserClassesCountByEmail(string $email): int {
        $query = "SELECT COUNT(c.classe_id) AS n FROM classi_dei_clienti c WHERE c.email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC)[0]["n"];
    }

    // Get the classes the user follows.
    public function getUserClassesByEmail(string $email): array {
        $query = "SELECT co.nome AS corso_nome, cl.id AS classe_id, cl.anno_accademico AS anno_accademico, " .
            "cl.nome AS classe_nome, cl.sezione, cl.professore, cl.assistente ".
            "FROM classi_dei_clienti cc, classi cl, corsi co ".
            "WHERE cc.email = ? AND cc.classe_id = cl.id AND cl.corso_id = co.id";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    // Get all the classes of a course.
    public function getClassesByCourseID(int $course_id, string $year = null): array {
    $query = "SELECT id, nome, sezione, anno_accademico, professore, assistente, corso_id FROM classi WHERE corso_id = ?";
        if ($year != null) {
            $query .= " AND anno_accademico = ?";
        }
        $stmt = $this->db->prepare($query);
        if ($year != null) {
            $stmt->bind_param('is', $course_id, $year);
        } else {
            $stmt->bind_param('i', $course_id);
        }
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    /**
    * BACHECA
    */
    public function getPostsNumberOfEmail($email){
        $query = "SELECT COUNT(p.id) AS nPosts FROM classi_dei_clienti c, pubblicazioni p WHERE c.email = ? AND c.classe_id = p.classe_id";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    public function getPostsOfEmail($email, $offset, $limit) {
        $query = "SELECT u.email AS email, c.id AS classe_id, p.id AS pubblicazione_id, u.img_profilo AS img_profilo, u.nome_utente AS nome_utente, co.nome AS corso_nome, c.nome AS classe_nome, c.sezione, c.anno_accademico AS anno_accademico, p.data_e_ora AS data_e_ora, p.testo AS testo, p.percorso AS percorso FROM classi_dei_clienti coc, pubblicazioni p, utenti u, classi c, corsi co WHERE coc.email = ? AND c.id = coc.classe_id AND p.classe_id = c.id AND p.email = u.email AND co.id = c.corso_id ORDER BY p.data_e_ora DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sii', $email, $limit, $offset);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    public function getProfessorNAssistantOfClass($id) {
        $query = "SELECT co.id AS corso_id, co.nome AS corso_nome, c.id AS id, c.nome AS nome, c.sezione AS sezione, c.anno_accademico AS anno_accademico, p.email AS professore_email, p.nome AS professore_nome, a.email AS assistente_email, a.nome AS assistente_nome FROM classi c JOIN professori p ON c.professore = p.email JOIN corsi co ON c.corso_id = co.id LEFT JOIN assistenti a ON c.assistente = a.email WHERE c.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    /**
        *CLASS
     */
    public function getPostsNumberOfClass($id){
        $query = "SELECT COUNT(p.id) AS nPosts FROM pubblicazioni p WHERE p.classe_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $risultato = $stmt->get_result();

        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    public function getPostsOfClass($id, $limit, $offset) {
        $query = "SELECT DISTINCT u.email AS email, p.id AS pubblicazione_id, u.img_profilo AS img_profilo, u.nome_utente AS nome_utente, p.data_e_ora AS data_e_ora, p.testo AS testo, p.percorso AS percorso FROM classi_dei_clienti coc, pubblicazioni p, utenti u WHERE coc.classe_id = ? AND p.classe_id = ? AND p.email = u.email ORDER BY p.data_e_ora DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiii', $id, $id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getResourcesOfClass($id, $limit, $offset) {
        $query = "SELECT percorso AS percorso FROM risorse_non_collegate WHERE classe_id = ? ORDER BY percorso LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
            *follow
     */
    public function getFollowStatusOfUserOfClass($email, $class) {
        $query = "SELECT COUNT(email) AS count FROM classi_dei_clienti WHERE email = ? AND classe_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $email, $class);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function userStartFollowClass($email, $class) {
        $query = "INSERT INTO classi_dei_clienti (email, classe_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $email, $class);
        return $stmt->execute();
    }

    public function userEndFollowClass($email, $class) {
        $query = "DELETE FROM classi_dei_clienti WHERE email = ? AND classe_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $email, $class);
        return $stmt->execute();
    }

    /**
     *      *resources
     */

    public function getResourcesNumberOfClass($id){
        $query = "SELECT COUNT(percorso) AS nResources FROM risorse_non_collegate WHERE classe_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function pathReplicateCheck($path) {
        $query = "SELECT COUNT(percorso) AS count FROM risorse_non_collegate WHERE percorso = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $path);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insertUnlinkedResources($email, $class, $path) {
        $query = "INSERT INTO risorse_non_collegate (email, classe_id, percorso) values (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sis', $email, $class, $path);
        return $stmt->execute();
    }

    public function deleteUnlinkedResources($path) {
        $query = "DELETE FROM risorse_non_collegate WHERE percorso = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $path);
        return $stmt->execute();
    }

    /**
     * ADMIN<
     */

    public function getAllAdminExcept($email) {
        $query = "SELECT email AS email, nome_utente AS nome_utente, attivo AS attivo, img_profilo AS img_profilo FROM utenti WHERE amministratore = 1 AND NOT email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function changeActiveStatus($email) {
        $query = "UPDATE utenti SET attivo = ? WHERE email = ?";
        $actualState = $this->checkIsActive($email);
        $newState = 0;
        if ($actualState[0]["attivo"] == 0) {
            $newState = 1;
        }
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('is', $newState, $email);
        return $stmt->execute();
    }

    public function getClientsNumber() {
        $query = "SELECT COUNT(email) AS count FROM utenti WHERE amministratore = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllClient($offset, $limit) {
        $query = "SELECT img_profilo AS img_profilo, email AS email, nome_utente AS nome_utente, attivo AS attivo FROM utenti WHERE amministratore = 0 ORDER BY email LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result =  $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function isAdmin($email) {
        $query = "SELECT amministratore AS admin FROM utenti WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addNewCourse($nome) {
        $query =  "INSERT INTO corsi (nome) VALUES (?)";
        $stmt =  $this->db->prepare($query);
        $stmt->bind_param('s', $nome);
        return $stmt->execute();
    }

    public function addNewSchoolYear($year) {
        $query =  "INSERT INTO anni_accademici (anno) VALUES (?)";
        $stmt =  $this->db->prepare($query);
        $stmt->bind_param('s', $year);
        return $stmt->execute();
    }

    public function getAllYears() {
        $query = "SELECT anno AS years FROM anni_accademici ORDER BY anno DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllProfessors() {
        $query = "SELECT email AS email, nome AS nome FROM professori";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllAssistants() {
        $query = "SELECT email AS email, nome AS nome FROM assistenti";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addNewClass($course, $nome, $year, $section, $professor, $assistant = null) {
        $query = "INSERT INTO classi (corso_id, anno_accademico, nome, sezione, professore, assistente) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('isssss', $course, $year, $nome, $section, $professor, $assistant);
        return $stmt->execute();
    }

    public function addNewProfessor($email, $nome) {
        $query ="INSERT INTO professori (email, nome) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $email, $nome);
        return $stmt->execute();
    }

    public function addNewAssistant($email, $nome) {
        $query ="INSERT INTO assistenti (email, nome) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $email, $nome);
        return $stmt->execute();
    }

    public function getYearsViaClassNameAndCourse($email=0, $idCorso=0, $nomeMateria=0){
        if($idCorso!=0 && $nomeMateria!=0 && $email!=0) {
            $result = [];
            $corso_int = (int) $idCorso;
            $query = "SELECT DISTINCT anni_accademici.anno AS anno_accademico FROM anni_accademici, classi, classi_dei_clienti, corsi WHERE anni_accademici.anno = classi.anno_accademico AND classi.id = classi_dei_clienti.classe_id AND classi.corso_id = corsi.id AND classi_dei_clienti.email = ? AND corsi.id = ? AND classi.nome = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('sis', $email, $corso_int, $nomeMateria);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getSectionOfUser($email=0, $idCorso=0, $nomeMateria=0, $anno=0) {
        if($idCorso!=0 && $nomeMateria!=0 && $email!=0 && $anno!=0) {
            $result = [];
            $corso_int = (int) $idCorso;
            $query = "SELECT DISTINCT classi.sezione AS sezione FROM anni_accademici, classi, corsi_dei_clienti, corsi WHERE anni_accademici.anno = classi.anno_accademico AND classi.corso_id = corsi.id AND corsi.id = corsi_dei_clienti.corso_id AND corsi_dei_clienti.email = ? AND corsi.id = ? AND classi.nome = ? AND anni_accademici.anno = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('siss', $email, $corso_int, $nomeMateria, $anno);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function insertNewPost($corso, $class, $section, $year, $text, $email) {
        $corso_int = (int) $corso;
        $id_class = 0;
        $id_class = $this->getClassIdViaClassSectionYearCourse($class, $section, $year, $corso_int);
        if($id_class != 0) {
            $query = "INSERT INTO pubblicazioni (testo, data_e_ora, classe_id, email) VALUES (?, NOW(), ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('sis', $text, $id_class, $email);
            $stmt->execute();
            if($this->db->insert_id) {
                return $this->db->insert_id;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function getClassIdViaClassSectionYearCourse($class, $section, $year, $corso_int) {
        $idClasse = 0;
        $query = "SELECT C.id AS idClasse FROM classi C WHERE C.nome = ? AND C.sezione = ? AND C.anno_accademico = ? AND C.corso_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssi', $class, $section, $year, $corso_int);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        $idClasse = (int) $result[0]["idClasse"];

        return $idClasse;
    }

    public function assignPathToPost($idPost, $finalPathFile){
        if($this->insertPostsResources($finalPathFile)) {
            $query = "UPDATE pubblicazioni SET percorso = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('si', $finalPathFile, $idPost);
            if($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function insertPostsResources($path) {
        $query = "INSERT INTO risorse_pubblicazioni (percorso) VALUES (?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $path);
        return $stmt->execute();
    }

    public function getInitialInfoOfPostViaId($idpost) {
        $query = "SELECT utenti.nome_utente AS nome_utente, utenti.img_profilo AS img_profilo, utenti.email AS email, corsi.nome AS corso_nome, classi.nome AS classe_nome, classi.sezione, classi.anno_accademico AS anno_accademico, pubblicazioni.data_e_ora AS data_e_ora, pubblicazioni.testo AS testo, pubblicazioni.percorso AS percorso FROM pubblicazioni, classi, corsi, utenti WHERE classi.id = pubblicazioni.classe_id AND corsi.id = classi.corso_id AND utenti.email = pubblicazioni.email AND pubblicazioni.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $idpost);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function getNextComments($idpostInt, $idCommentInt, $maxCommentsInt, $dateAndHourComment) {
        $query = "SELECT commenti.id AS id, utenti.img_profilo AS img_profilo, utenti.email AS email, utenti.nome_utente AS nome_utente, commenti.testo AS testo, commenti.percorso AS percorso, commenti.data_e_ora AS data_e_ora
            FROM commenti, utenti
            WHERE commenti.pubblicazione_id = ?
                AND commenti.email = utenti.email";
        if($idCommentInt == 0) {
            $query .= "
            ORDER BY commenti.data_e_ora ASC, commenti.id ASC
            LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $idpostInt, $maxCommentsInt);
        } else {
            $query .= "
                AND (commenti.data_e_ora > ?
                    OR (commenti.data_e_ora = ?
                    AND commenti.id > ?))
            ORDER BY commenti.data_e_ora ASC, commenti.id ASC
            LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('issii', $idpostInt, $dateAndHourComment, $dateAndHourComment, $idCommentInt, $maxCommentsInt);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCommentPathViaPostID($idPostInt){
        $query = "SELECT classi.corso_id AS corso_id, classi.sezione AS sezione, classi.id AS classe_id, classi.anno_accademico AS anno_accademico FROM pubblicazioni, classi WHERE pubblicazioni.classe_id = classi.id AND pubblicazioni.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $idPostInt);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if(!$result) {
            return null;
        }
        $path = $result["corso_id"]."/".$result["classe_id"]."/".$result["anno_accademico"]."-".$result["sezione"]."/posts/".$idPostInt."/comments/";
        return $path;
    }

    public function isFreeCommentPath($path){
        $query = "SELECT percorso FROM commenti WHERE percorso = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $path);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0){
            return true;
        }
        return false;
    }

    public function insertNewComment($email, $idpostInt, $text){
        $query = "INSERT INTO commenti (testo, data_e_ora, pubblicazione_id, email) VALUES (?, NOW(), ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sis', $text, $idpostInt, $email);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        } else {
            return false;
        }
    }

    public function assignPathToComment($idComment, $pathToBeSaved){
        if($this->insertCommentsResources($pathToBeSaved)){
            $query = "UPDATE commenti SET percorso = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('si', $pathToBeSaved, $idComment);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function insertCommentsResources($path) {
        $query = "INSERT INTO risorse_commenti (percorso) VALUES (?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $path);
        return $stmt->execute();
    }

    public function getPathOfComment($idComment){
        $query = "SELECT commenti.percorso AS percorso FROM commenti WHERE commenti.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idComment);
        if($stmt->execute()){
            $riga = $stmt->get_result()->fetch_assoc();
            if($riga && !empty($riga["percorso"])){
                return $riga["percorso"];
            }
        }
        return "";
    }

    public function removeComment($idComment){
        $query = "DELETE FROM commenti WHERE commenti.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idComment);
        $imgPath = $this->getPathOfComment($idComment);
        if($stmt->execute()){
            if($imgPath != ""){
                return $this->removeCommentResources($imgPath);
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function removePathOfComment($idComment){
        $imgPath = $this->getPathOfComment($idComment);
        $query = "UPDATE commenti SET percorso = NULL WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idComment);
        if($stmt->execute()) {
            if($this->removeCommentResources($imgPath)) {
                return true;
            }
        }
        return false;
    }

    /* Nella query seguente si è certi che il percorso è presente */
    public function removeCommentResources($imgPath){
        $query = "DELETE FROM risorse_commenti WHERE risorse_commenti.percorso = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $imgPath);
        return $stmt->execute();
    }

    public function updateTextOfComment($idComment, $text){
        $query = "UPDATE commenti SET testo = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $text, $idComment);
        return $stmt->execute();
    }

    public function getPathOfPost($idPost){
        $query = "SELECT pubblicazioni.percorso AS percorso FROM pubblicazioni WHERE pubblicazioni.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idPost);
        if($stmt->execute()){
            $riga = $stmt->get_result()->fetch_assoc();
            if($riga && !empty($riga["percorso"])){
                return $riga["percorso"];
            }
        }
        return "";
    }

    public function removePathOfPost($idPost){
        $imgPath = $this->getPathOfPost($idPost);
        $query = "UPDATE pubblicazioni SET percorso = NULL WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idPost);
        if($stmt->execute()) {
            if($this->removePostResources($imgPath)) {
                return true;
            }
        }
        return false;
    }

    public function removePostResources($imgPostPath){
        $query = "DELETE FROM risorse_pubblicazioni WHERE risorse_pubblicazioni.percorso = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $imgPostPath);
        return $stmt->execute();
    }

    public function getAllCommentsViaPostId($idPost){
        $query = "SELECT commenti.id AS id, commenti.percorso AS percorso FROM pubblicazioni, commenti WHERE pubblicazioni.id = commenti.pubblicazione_id AND pubblicazioni.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idPost);
        $stmt->execute();
        $risultato = $stmt->get_result();
        return $risultato->fetch_all(MYSQLI_ASSOC);
    }

    public function removePost($idPost){
        $query = "DELETE FROM pubblicazioni WHERE pubblicazioni.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idPost);
        return $stmt->execute();
    }

    public function updateTextOfPost($idPost, $text){
        $query = "UPDATE pubblicazioni SET testo = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $text, $idPost);
        return $stmt->execute();
    }
}
?>
