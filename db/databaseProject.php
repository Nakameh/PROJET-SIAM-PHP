<?php

class DataBaseProject
{
    private PDO $pdo;

    private PDOStatement $prepareCreateUser;
    private PDOStatement $prepareCreatePasswordUser;
    private PDOStatement $prepareUserExist;
    private PDOStatement $prepareIsAdmin;
    private PDOStatement $prepareGetUserId;
    private PDOStatement $prepareGetHash;
    private PDOStatement $prepareGetUsername;
    private PDOStatement $prepareUpdateDateLastSeen;
    private PDOStatement $prepareUpdatePassword;
    private PDOStatement $prepareGetActivesUsers;


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

        $this->prepareCreateUser = $this->pdo->prepare("INSERT INTO User
                (username_User, isAdmin_User, dateLastSeen_User) VALUES (:username, :isAdmin, :dateLastSeen)");
        
        $this->prepareCreatePasswordUser = $this->pdo->prepare("INSERT INTO PasswordUser
                (hash_PasswordUser, id_User) VALUES (:hash, :id_User)");

        $this->prepareUserExist = $this->pdo->prepare("SELECT * FROM User WHERE username_User = :username");
        $this->prepareIsAdmin = $this->pdo->prepare("SELECT isAdmin_User FROM User WHERE id_User = :id_User");
        $this->prepareGetUserId = $this->pdo->prepare("SELECT id_User FROM User WHERE username_User = :username");
        $this->prepareGetHash = $this->pdo->prepare("SELECT hash_PasswordUser FROM PasswordUser
            WHERE id_User = :id_User");
        $this->prepareGetUsername = $this->pdo->prepare("SELECT username_User FROM User WHERE id_User = :id_User");
        $this->prepareUpdateDateLastSeen = $this->pdo->prepare("UPDATE User
                SET dateLastSeen_User = :dateLastSeen WHERE id_User = :id_User");

        $this->prepareUpdatePassword = $this->pdo->prepare("UPDATE PasswordUser
                SET hash_PasswordUser = :hash WHERE id_User = :id_User");
        
        $this->prepareGetActivesUsers = $this->pdo->prepare("SELECT username_User FROM User
            WHERE dateLastSeen_User > :dateLastSeen");

        if (!$this->userExist("admin")) {
            $this->createUser("admin", 1, password_hash("admin", PASSWORD_DEFAULT));
        }
    }

    private function createGameTable() :void {
        $sql = "CREATE TABLE IF NOT EXISTS Game ( id_Game INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                            game_board_Game VARCHAR(512) NOT NULL,
                            nb_turn_Game INT NOT NULL,
                            date_debut_Game DATETIME NOT NULL,
                            date_fin_Game DATETIME,
                            status_Game INT NOT NULL,
                            id_gameuser1 INTEGER NOT NULL,
                            id_gameuser2 INTEGER,
                            id_userplaying INTEGER,
                            id_user_winner INTEGER);";
        $this->pdo->exec($sql);
    }


    private function createUserTable():void{
        $sql = "CREATE TABLE IF NOT EXISTS User (
                        id_User INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                        username_User VARCHAR(40) NOT NULL,
                        isAdmin_User INTEGER NOT NULL,
                        dateLastSeen_User DATETIME NOT NULL);";
        $this->pdo->exec($sql);
    }


    private function createPasswordTable():void{
        $sql = "CREATE TABLE IF NOT EXISTS PasswordUser (
                id_PasswordUser INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                hash_PasswordUser VARCHAR(128) NOT NULL,
                id_User INTEGER NOT NULL);";
        $this->pdo->exec($sql);
    }

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

    public function createUser(string $username, bool $isAdmin, string $hash):void{
        $this->prepareCreateUser->execute([
            ":username" => $username,
            ":isAdmin" => $isAdmin,
            ":dateLastSeen" => date("Y-m-d H:i:s")
        ]);
        $id_User = $this->pdo->lastInsertId();
        $this->prepareCreatePasswordUser->execute([
            ":hash" => $hash,
            ":id_User" => $id_User
        ]);
    }

    public function userExist(string $username): bool{
        $this->prepareUserExist->execute([":username" => $username]);
        return $this->prepareUserExist->fetch(PDO::FETCH_ASSOC) != false;
    }

    public function isAdmin(int $id_User): bool{
        $this->prepareIsAdmin->execute([":id_User" => $id_User]);
        return $this->prepareIsAdmin->fetch(PDO::FETCH_ASSOC)["isAdmin_User"] == 1;
    }

    public function getUserId(string $username) {
        $this->prepareGetUserId->execute([":username" => $username]);
        return $this->prepareGetUserId->fetch(PDO::FETCH_ASSOC)["id_User"];
    }


    public function getHash($id_User) {
        $this->prepareGetHash->execute([":id_User" => $id_User]);
        return $this->prepareGetHash->fetch(PDO::FETCH_ASSOC)["hash_PasswordUser"];
    }

    public function getUsername($id_User) {
        $this->prepareGetUsername->execute([":id_User" => $id_User]);
        return $this->prepareGetUsername->fetch(PDO::FETCH_ASSOC)["username_User"];
    }

    public function updateDateLastSeen(int $id_User):void{
        $this->prepareUpdateDateLastSeen->execute([
            ":dateLastSeen" => date("Y-m-d H:i:s"),
            ":id_User" => $id_User
        ]);
    }

    public function updatePassword(int $id_User, string $hash):void{
        $this->prepareUpdatePassword->execute([
            ":hash" => $hash,
            ":id_User" => $id_User
        ]);
    }

    public function getActivesUsers() {
        $this->prepareGetActivesUsers->execute([":dateLastSeen" => date("Y-m-d H:i:s", strtotime("-5 minutes"))]);
        return $this->prepareGetActivesUsers->fetchAll(PDO::FETCH_ASSOC);
    }

}
