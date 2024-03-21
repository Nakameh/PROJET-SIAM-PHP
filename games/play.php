<?php
require_once "../vue/vue.php";

require_once "../db/databaseProject.php";
require_once "gameClass.php";

session_start();
$dbp = new DataBaseProject("../db/projet.sqlite3");

header("Content-Type: application/json");


if (!isset($_GET['idGame'], $_GET['action'], $_GET['userId'])) {
    echo "false";
    exit();
}


$id_game = $_GET['idGame'];
$action = $_GET['action'];
$id_user = $_GET['userId'];

$isAdmin = $dbp->isAdmin($id_user);


$gamedb = $dbp->getGame($id_game);
if (!$gamedb) {
    echo "false";
    exit();
}

$board = $gamedb['game_board_Game'];
$boardObject = json_decode($board);

$game = new Game(   $boardObject->lines,
                    $boardObject->columns,
                    $boardObject->deckSize,
                    $gamedb["id_gameuser1"],
                    $gamedb["id_gameuser2"],
                    $boardObject->activePlayer,
                    $boardObject->board,
                    $boardObject->deckPlayer1,
                    $boardObject->deckPlayer2);


if ($game->getUserPlayingId() != $id_user && !$isAdmin) {
    echo "false";
    exit();
}



if ($action == "addPawn") {
    if (!isset($_GET['line'], $_GET['column'], $_GET['pawn'], $_GET['rotation'], $_GET['indexDeck'], $_GET['pushDirection'])) {
        echo "false";
        exit();
    }

    $line = $_GET['line'];
    $column = $_GET['column'];
    $pawn = $_GET['pawn'];
    $rotation = $_GET['rotation'];
    $indexDeck = $_GET['indexDeck'];
    $pushDirection = $_GET['pushDirection'];

    $ret = $game->addPawn($line, $column, $pawn, $rotation, $indexDeck, $id_user, $pushDirection);
    if (!$ret) {
        echo "false";
        exit();
    }

    $dbp->updateBoard($id_game, $game->jsonSerialize());
    $turn = $dbp->getTurn($id_game);
    $dbp->addTurn($id_game);
    $dbp->createMovement(time(), $line."-".$column, $line."-".$column, $action, $turn, $id_game, $id_user);

    if ($game->getWinner() != -1) {
        $dbp->setIdWinner($id_game, $game->getWinner());
    }

    echo "true";
    exit();
}


if ($action == "rotatePawn") {

    if (!isset($_GET['line'], $_GET['column'], $_GET['pawn'], $_GET['rotation'])) {
        echo "false";
        exit();
    }

    $line = $_GET['line'];
    $column = $_GET['column'];
    $rotation = $_GET['rotation'];
    $pawn = $_GET['pawn'];

    $ret = $game->rotatePawn($line, $column, $pawn, $rotation);
    if (!$ret) {
        echo "false";
        exit();
    }

    $dbp->updateBoard($id_game, $game->jsonSerialize());
    $turn = $dbp->getTurn($id_game);
    $dbp->addTurn($id_game);

    $dbp->createMovement(time(), $line."-".$column, $line."-".$column, $action, $turn, $id_game, $id_user);

    echo "true";
    exit();
}


if ($action == "movePawn") {
    if (!isset($_GET['line'], $_GET['column'], $_GET['pasX'], $_GET['pasY'], $_GET['pawn'], $_GET['rotation'])) {
        echo "false";
        exit();
    }
    $line = $_GET['line'];
    $column = $_GET['column'];
    $pasX = $_GET['pasX'];
    $pasY = $_GET['pasY'];
    $pawn = $_GET['pawn'];
    $rotation = $_GET['rotation'];


    $ret = $game->movePawn($line, $column, $pasX, $pasY, $pawn, $rotation, $id_user);

    if (!$ret) {
        echo "false";
        exit();
    }

    $dbp->updateBoard($id_game, $game->jsonSerialize());
    $turn = $dbp->getTurn($id_game);
    $dbp->addTurn($id_game);

    $dbp->createMovement(time(), $line."-".$column, ($line + $pasY)."-".($column + $pasX), $action, $turn, $id_game, $id_user);

    if ($game->getWinner() != -1) {
        $dbp->setIdWinner($id_game, $game->getWinner());
    }

    echo "true";
    exit();
}

echo "false";
exit();
