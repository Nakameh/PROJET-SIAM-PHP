<?php

class DataBaseProject
{
    private PDO $pdo;




    /**
     * Constructeur de la classe DataBaseProject
     * @param string $path : chemin vers la base de données
     * @return void
     */
    public function __construct(string $path) {
        try {
            $this->pdo = new PDO("sqlite:$path");
        } catch (\Throwable $th) {
            exit("Erreur lors de la connexion à la base de données");
        }
        $this->createGameTable();
        $this->createUserTable();
        $this->createPasswordTable();
        $this->createMovementTable();


        if (!$this->userExist("admin")) {
            $this->createUser("admin", 1, password_hash("admin", PASSWORD_DEFAULT));
        }
        if (!$this->userExist("noah")) {
            $this->createUser("noah", 0, password_hash("noah", PASSWORD_DEFAULT));
        }
        if (!$this->userExist("capu")) {
            $this->createUser("capu", 0, password_hash("capu", PASSWORD_DEFAULT));
        }
    }




    /**
     * Crée la table Game de la base de données
     * @return void
     */
    private function createGameTable() :void {
        $sql = "CREATE TABLE IF NOT EXISTS Game ( id_Game INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                            game_board_Game VARCHAR(512) NOT NULL,
                            nb_turn_Game INT NOT NULL,
                            date_debut_Game DATETIME NOT NULL,
                            date_fin_Game DATETIME,
                            status_Game INT NOT NULL,
                            id_gameuser1 INTEGER NOT NULL,
                            id_gameuser2 INTEGER,
                            id_user_winner INTEGER);";
        $this->pdo->exec($sql);
    }




    /**
     * Crée la table User de la base de données
     * @return void
     */
    private function createUserTable():void{
        $sql = "CREATE TABLE IF NOT EXISTS User (
                        id_User INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                        username_User VARCHAR(40) NOT NULL,
                        isAdmin_User INTEGER NOT NULL,
                        dateLastSeen_User DATETIME NOT NULL);";
        $this->pdo->exec($sql);
    }




    /**
     * Crée la table PasswordUser de la base de données
     * @return void
     */
    private function createPasswordTable():void{
        $sql = "CREATE TABLE IF NOT EXISTS PasswordUser (
                id_PasswordUser INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                hash_PasswordUser VARCHAR(128) NOT NULL,
                id_User INTEGER NOT NULL);";
        $this->pdo->exec($sql);
    }




    /**
     * Crée la table Movement de la base de données
     * @return void
     */
    private function createMovementTable():void{
        $sql = "CREATE TABLE IF NOT EXISTS Movement (
                id_Movement INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                date_Movement DATETIME NOT NULL,
                position_initiale_Movement VARCHAR(20) NOT NULL,
                position_finale_Movement VARCHAR(20) NOT NULL,
                type_Movement VARCHAR(20) NOT NULL,
                turn_Movement INT NOT NULL,
                id_Game INTEGER NOT NULL,
                id_User INTEGER NOT NULL);";
        $this->pdo->exec($sql);
    }




    /**
     * Permet de créer un utilisateur dans la table User et PasswordUser de la base de données
     * @param string $username : nom de l'utilisateur
     * @param bool $isAdmin : si l'utilisateur est un administrateur
     * @param string $hash : mot de passe de l'utilisateur
     * @return void
     */
    public function createUser(string $username, bool $isAdmin, string $hash):void{
        $prepareCreateUser = $this->pdo->prepare("INSERT INTO User
            (username_User, isAdmin_User, dateLastSeen_User) VALUES (:username, :isAdmin, :dateLastSeen)");

        $prepareCreatePasswordUser = $this->pdo->prepare("INSERT INTO PasswordUser
            (hash_PasswordUser, id_User) VALUES (:hash, :id_User)");

        $prepareCreateUser->execute([
            ":username" => $username,
            ":isAdmin" => $isAdmin,
            ":dateLastSeen" => 0
        ]);

        $id_User = $this->pdo->lastInsertId();
        $prepareCreatePasswordUser->execute([
            ":hash" => $hash,
            ":id_User" => $id_User
        ]);
        $prepareCreateUser->closeCursor();
        $prepareCreatePasswordUser->closeCursor();
    }




    /**
     * Permet de vérifier si un utilisateur existe
     * @param string $username : nom de l'utilisateur
     * @return bool : true si l'utilisateur existe, false sinon
     */
    public function userExist(string $username): bool{
        $prepareUserExist = $this->pdo->prepare("SELECT * FROM User WHERE username_User = :username");
        $prepareUserExist->execute([":username" => $username]);
        $res = $prepareUserExist->fetch(PDO::FETCH_ASSOC) != false;
        $prepareUserExist->closeCursor();
        return $res;
    }




    /**
     * Permet de vérifier si un utilisateur est un administrateur
     * @param int $id_User : id de l'utilisateur
     * @return bool : true si l'utilisateur est un administrateur, false sinon
     */
    public function isAdmin(int $id_User): bool{
        $prepareIsAdmin = $this->pdo->prepare("SELECT isAdmin_User FROM User WHERE id_User = :id_User");
        $prepareIsAdmin->execute([":id_User" => $id_User]);
        $res = $prepareIsAdmin->fetch(PDO::FETCH_ASSOC)["isAdmin_User"] == 1;
        $prepareIsAdmin->closeCursor();
        return $res;
    }




    /**
     * Permet récupérer l'id d'un utilisateur
     * @param string $username : nom de l'utilisateur
     * @return int : id de l'utilisateur
     */
    public function getUserId(string $username): int {
        $prepareGetUserId = $this->pdo->prepare("SELECT id_User FROM User WHERE username_User = :username
            LIMIT 1");
        $prepareGetUserId->execute([":username" => $username]);
        $res = $prepareGetUserId->fetch(PDO::FETCH_ASSOC)["id_User"];
        $prepareGetUserId->closeCursor();
        return $res;
    }




    /**
     * Permet de récupérer le hash du mot de passe d'un utilisateur
     * @param int $id_User : id de l'utilisateur
     * @return string : hash du mot de passe
     */
    public function getHash($id_User):string {
        $prepareGetHash = $this->pdo->prepare("SELECT hash_PasswordUser FROM PasswordUser
            WHERE id_User = :id_User");
        $prepareGetHash->execute([":id_User" => $id_User]);
        $res = $prepareGetHash->fetch(PDO::FETCH_ASSOC)["hash_PasswordUser"];
        $prepareGetHash->closeCursor();
        return $res;
    }




    /**
     * Permet de récupérer le nom d'un utilisateur
     * @param int $id_User : id de l'utilisateur
     * @return string : nom de l'utilisateur
     */
    public function getUsername($id_User) : string {
        $prepareGetUsername = $this->pdo->prepare("SELECT username_User FROM User WHERE id_User = :id_User");
        $prepareGetUsername->execute([":id_User" => $id_User]);
        $res = $prepareGetUsername->fetch(PDO::FETCH_ASSOC)["username_User"];
        $prepareGetUsername->closeCursor();
        return $res;
    }




    /**
     * Permet de mettre à jour la date de dernière connexion d'un utilisateur
     * @param int $id_User : id de l'utilisateur
     * @return void
     */
    public function updateDateLastSeen(int $id_User):void{
        $prepareUpdateDateLastSeen = $this->pdo->prepare("UPDATE User
            SET dateLastSeen_User = :dateLastSeen WHERE id_User = :id_User");
        $prepareUpdateDateLastSeen->execute([
            ":dateLastSeen" => date("Y-m-d H:i:s"),
            ":id_User" => $id_User
        ]);
    }




    /**
     * Permet de mettre à jour le mot de passe d'un utilisateur
     * @param int $id_User : id de l'utilisateur
     * @param string $hash : nouveau mot de passe
     * @return void
     */
    public function updatePassword(int $id_User, string $hash):void{
        $prepareUpdatePassword = $this->pdo->prepare("UPDATE PasswordUser
            SET hash_PasswordUser = :hash WHERE id_User = :id_User");
        $prepareUpdatePassword->execute([
            ":hash" => $hash,
            ":id_User" => $id_User
        ]);
    }




    /**
     * Permet de récupérer la liste des utilisateurs actifs
     * @return array : liste des utilisateurs actifs
     */
    public function getActivesUsers():array {
        $prepareGetActivesUsers = $this->pdo->prepare("SELECT username_User FROM User
            WHERE dateLastSeen_User > :dateLastSeen");
        $prepareGetActivesUsers->execute([":dateLastSeen" => date("Y-m-d H:i:s", strtotime("-5 minutes"))]);
        $res = $prepareGetActivesUsers->fetchAll(PDO::FETCH_ASSOC);
        $prepareGetActivesUsers->closeCursor();
        return $res;
    }




    /**
     * Permet de vérifier si un id d'utilisateur existe
     * @param int $id_User : id de l'utilisateur
     * @return bool : true si l'id existe, false sinon
     */
    public function idUserExist(int $id_User): bool{
        $prepareIdUserExist = $this->pdo->prepare("SELECT * FROM User WHERE id_User = :id_User");
        $prepareIdUserExist->execute([":id_User" => $id_User]);
        $res = $prepareIdUserExist->fetch(PDO::FETCH_ASSOC) != false;
        $prepareIdUserExist->closeCursor();
        return $res;
    }




    /**
     * Permet de créer une nouvelle partie dans la table Game de la base de données
     * @param string $game_board_Game : plateau de jeu
     * @param int $nb_turn_Game : nombre de tours
     * @param string $date_debut_Game : date de début de la partie
     * @param int $status_Game : statut de la partie
     * @param int $id_gameuser1 : id du joueur 1
     * @return string : id de la partie
     */
    public function createGame(string $game_board_Game, int $nb_turn_Game,
                                string $date_debut_Game, int $status_Game, int $id_gameuser1):string{
        $prepareCreateGame = $this->pdo->prepare("INSERT INTO Game
            (game_board_Game, nb_turn_Game, date_debut_Game, status_Game, id_gameuser1) VALUES
            (:game_board_Game, :nb_turn_Game, :date_debut_Game, :status_Game, :id_gameuser1)");
        $prepareCreateGame->execute([
            ":game_board_Game" => $game_board_Game,
            ":nb_turn_Game" => $nb_turn_Game,
            ":date_debut_Game" => $date_debut_Game,
            ":status_Game" => $status_Game,
            ":id_gameuser1" => $id_gameuser1
        ]);
        return $this->pdo->lastInsertId();
    }




    /**
     * Permet de récupérer la liste des parties avec un seul joueur
     * @return array : liste des parties avec un seul joueur
     */
    public function getListGamesWith1Player() : array {
        $prepareGetListGamesWith1Player = $this->pdo->prepare("SELECT * FROM Game WHERE id_gameuser2 IS NULL");
        $prepareGetListGamesWith1Player->execute();
        $res = $prepareGetListGamesWith1Player->fetchAll(PDO::FETCH_ASSOC);
        $prepareGetListGamesWith1Player->closeCursor();
        return $res;
    }




    /**
     * Permet de récupérer une partie en fonction de son id
     * @param int $id_Game : id de la partie
     * @return array : partie correspondante à l'id
     */
    public function getGame(int $id_Game) {
        $prepareGetGame = $this->pdo->prepare("SELECT * FROM Game WHERE id_Game = :id_Game");
        $prepareGetGame->execute([":id_Game" => $id_Game]);
        $res = $prepareGetGame->fetch(PDO::FETCH_ASSOC);
        $prepareGetGame->closeCursor();
        return $res;
    }




    /**
     * Permet d'ajouter un joueur à une partie en fonction de son id
     * @param int $id_Game : id de la partie
     * @param int $id_gameuser2 : id du joueur 2
     * @return void
     */
    public function addGameUser2(int $id_Game, int $id_gameuser2) {
        $prepareAddGameUser2 = $this->pdo->prepare("UPDATE Game SET id_gameuser2 = :id_gameuser2
            WHERE id_Game = :id_Game AND id_gameuser2 IS NULL");
        $prepareAddGameUser2->execute([
            ":id_Game" => $id_Game,
            ":id_gameuser2" => $id_gameuser2
        ]);
        $prepareAddGameUser2->closeCursor();
    }




    /**
     * Permet de récupérer les parties d'un utilisateur
     * @param int $id_user : id de l'utilisateur
     * @return array : liste des parties de l'utilisateur
     */
    public function getMyGames(int $id_user):array {
        $prepareGetMyGames = $this->pdo->prepare("SELECT * FROM Game
            WHERE id_gameuser1 = :id_user OR id_gameuser2 = :id_user");
        $prepareGetMyGames->execute([":id_user" => $id_user]);
        $res = $prepareGetMyGames->fetchAll(PDO::FETCH_ASSOC);
        $prepareGetMyGames->closeCursor();
        return $res;
    }




    /**
     * Permet de vérifier si une partie a 2 joueurs
     * @param int $id_Game : id de la partie
     * @return bool : true si la partie a 2 joueurs, false sinon
     */
    public function gameHas2Players(int $id_Game): bool{
        $prepareGameHas2Players = $this->pdo->prepare("SELECT * FROM Game WHERE id_Game = :id_Game AND id_gameuser2 IS NOT NULL");
        $prepareGameHas2Players->execute([":id_Game" => $id_Game]);
        $res = $prepareGameHas2Players->fetch(PDO::FETCH_ASSOC) != false;
        $prepareGameHas2Players->closeCursor();
        return $res;
    }




    /**
     * Permet de récupérer une liste de toutes les parties
     * @return array : liste des parties
     */
    public function getGames() {
        $sql = "SELECT * FROM Game";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    /**
     * Permet de supprimer une partie en fonction de son id
     * @param int $id_Game : id de la partie
     * @return void
     */
    public function deleteGame(int $id_Game) {
        $prepareDeleteGame = $this->pdo->prepare("DELETE FROM Game WHERE id_Game = :id_Game");
        $prepareDeleteGame->execute([":id_Game" => $id_Game]);
        $prepareDeleteGame->closeCursor();
    }




    /**
     * Permet de mettre à jour le plateau de jeu d'une partie
     * @param int $id_game : id de la partie
     * @param string $board : plateau de jeu
     * @return void
     */
    public function updateBoard(int $id_game, string $board) {
        $prepareUpdateBoard = $this->pdo->prepare("UPDATE Game SET game_board_Game = :game_board_Game
            WHERE id_Game = :id_Game");
        $prepareUpdateBoard->execute(["id_Game" => $id_game, "game_board_Game" => $board]);
        $prepareUpdateBoard->closeCursor();
    }




    /**
     * Permet de définir l'id du gagnant d'une partie
     * @param int $id_Game : id de la partie
     * @param int $id_user_winner : id du gagnant
     */
    public function setIdWinner(int $id_Game, int $id_user_winner) {
        $prepareSetIdWinner = $this->pdo->prepare("UPDATE Game SET id_user_winner = :id_user_winner
            WHERE id_Game = :id_Game");
        $prepareSetIdWinner->execute([":id_Game" => $id_Game, ":id_user_winner" => $id_user_winner]);
        $prepareSetIdWinner->closeCursor();
    }




    /**
     * Renvoie l'id du gagnant d'une partie
     * @param int $id_Game : id de la partie
     * @return : id du gagnant
     */
    public function getIdWinner(int $id_Game){
        $sql = "SELECT id_user_winner FROM Game WHERE id_Game = :id_Game AND id_user_winner IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id_Game" => $id_Game]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["id_user_winner"];
    }




    /**
     * Permet d'ajouter un au nombre de tours d'une partie
     * @param int $id_Game : id de la partie
     * @return void
     */
    public function addTurn(int $id_Game) {
        $prepareAddTurn = $this->pdo->prepare("UPDATE Game SET nb_turn_Game = nb_turn_Game + 1
            WHERE id_Game = :id_Game");
        $prepareAddTurn->execute([":id_Game" => $id_Game]);
        $prepareAddTurn->closeCursor();
    }




    /**
     * Permet de récupérer le nombre de tours d'une partie
     * @param int $id_Game : id de la partie
     * @return int : nombre de tours
     */
    public function getTurn(int $id_Game) {
        $sql = "SELECT nb_turn_Game FROM Game WHERE id_Game = :id_Game";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id_Game" => $id_Game]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["nb_turn_Game"];
    }




    /**
     * Permet de créer un mouvement dans la table Movement de la base de données
     * @param string $date_Movement : date du mouvement
     * @param string $position_initiale_Movement : position initiale du mouvement
     * @param string $position_finale_Movement : position finale du mouvement
     * @param string $type_Movement : type de mouvement
     * @param int $turn_Movement : tour du mouvement
     * @param int $id_Game : id de la partie
     * @param int $id_User : id de l'utilisateur
     * @return void
     */
    public function createMovement(string $date_Movement, string $position_initiale_Movement,
                                    string $position_finale_Movement, string $type_Movement,
                                    int $turn_Movement, int $id_Game, int $id_User):void{
        $prepareCreateMovement = $this->pdo->prepare("INSERT INTO Movement
            (date_Movement, position_initiale_Movement, position_finale_Movement, type_Movement,
            turn_Movement, id_Game, id_User) VALUES
            (:date_Movement, :position_initiale_Movement, :position_finale_Movement, :type_Movement,
            :turn_Movement, :id_Game, :id_User)");
        $prepareCreateMovement->execute([
            ":date_Movement" => $date_Movement,
            ":position_initiale_Movement" => $position_initiale_Movement,
            ":position_finale_Movement" => $position_finale_Movement,
            ":type_Movement" => $type_Movement,
            ":turn_Movement" => $turn_Movement,
            ":id_Game" => $id_Game,
            ":id_User" => $id_User
        ]);
        $prepareCreateMovement->closeCursor();
    }




    /**
     * Permet de récupérer le dernier mouvement d'une partie
     * @param int $id_Game : id de la partie
     * @return array : dernier mouvement
     */
    public function getLastMovement(int $id_Game) {
        $sql = "SELECT * FROM Movement WHERE id_Game = :id_Game ORDER BY id_Movement DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id_Game" => $id_Game]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
