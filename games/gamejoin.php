<?php
require_once "../vue/head.php";
require_once "../vue/body.php";
require_once "../db/databaseProject.php";

session_start();

$dbp = new DataBaseProject("../db/projet.sqlite3");
$isAdmin = false;
$isConnected = false;

if (isset($_SESSION['id_user'])) {
    if (!$dbp->idUserExist($_SESSION['id_user'])) {
        header("Location: ../login/disconnect.php");
        exit();
    }
    $id_user = $_SESSION['id_user'];
    $username = $dbp->getUsername($id_user);
    $isAdmin = $dbp->isAdmin($id_user);
    $dbp->updateDateLastSeen($id_user);
    $isConnected = true;
} else {
    header("Location: ../games/listgames.php");
    exit();
}

if (!isset($_GET['idGame'])) {
    header("Location: ../games/listgames.php");
    exit();
}

$idGame = $_GET['idGame'];

$game = $dbp->getGame($idGame);

if ($game == null) {
    header("Location: ../games/listgames.php");
    exit();
}

if ($game["id_gameuser1"] == $id_user || $game["id_gameuser2"] == $id_user) {
    if (empty($game["id_gameuser2"])) {
        header("Location: gamewait.php?idGame=" . $idGame);
        exit();
    }
    header("Location: ../games/game.php?idGame=" . $idGame);
    exit();
}

if ($game["id_gameuser2"] != 0) {
    header("Location: ../games/listgames.php");
    exit();
}

$dbp->addGameUser2($idGame, $id_user);

header("Location: ../games/game.php?idGame=" . $idGame);
exit();
