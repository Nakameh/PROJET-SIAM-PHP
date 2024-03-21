<?php
require_once "../vue/vue.php";

require_once "../db/databaseProject.php";

session_start();

$dbp = new DataBaseProject("../db/projet.sqlite3");
$isAdmin = false;

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit();
} else {
    if (!$dbp->idUserExist($_SESSION['id_user'])) {
        header("Location: ../login/disconnect.php");
        exit();
    }
    $id_user = $_SESSION['id_user'];
    $username = $dbp->getUsername($id_user);
    $isAdmin = $dbp->isAdmin($id_user);
    if (!$isAdmin) {
        header("Location: ../index.php");
        exit();
    }
    $dbp->updateDateLastSeen($id_user);
}

if (isset($_GET['idGame'])) {
    $id_game = $_GET['idGame'];
    $dbp->deleteGame($id_game);
    header("Location: gamedeletelist.php");
    exit();
}
