<?php
require_once "../db/databaseProject.php";
require_once "gameClass.php";
header("Content-Type: application/javascript");
$dbp = new DataBaseProject("../db/projet.sqlite3");




if (isset($_GET['idGame']) && isset($_GET['action'])) {

    $action = $_GET['action'];
    $idGame = $_GET['idGame'];
    $game = $dbp->getGame($idGame);

    if ($action == "gameExist") {
        if ($game == false) {
            echo "true";
            exit();
        } else {
            echo "false";
            exit();
        }
    }

    if ($action == "getUserName") {
        if (isset($_GET['id_user'])) {
            $id_user = $_GET['id_user'];
            $username = $dbp->getUsername($id_user);
            $turn = $dbp->getTurn($idGame);
            echo json_encode(array("username" => $username, "turn" => $turn));
            exit();
        } else {
            echo "false";
            exit();
        
        }
    }

    if ($action == "gameFinished") {
        if ($game == false) {
            echo "false";
            exit();
        }
        $idwinner = $dbp->getIdWinner($idGame);
        if ($idwinner == false) {
            echo "false";
            exit();
        }
        echo "true";
        exit();
    }

    if ($action == "getLastMove") {
        if ($game == false) {
            echo "false";
            exit();
        }
        $lastMove = $dbp->getLastMovement($idGame);
        if ($lastMove == false) {
            echo "false";
            exit();
        }
        echo json_encode($lastMove);
        exit();
    
    }

    if ($action == "getLastMoveByPlayer") {
        if (isset($_GET['id_user'])) {
            $id_user = $_GET['id_user'];
            $lastMove = $dbp->getLastMovementByUser($idGame, $id_user);
            if ($lastMove == false) {
                echo "false";
                exit();
            }
            
            echo json_encode($lastMove);
            exit();
        } else {
            echo "false";
            exit();
        }
    }
}






$res = "";

if (isset($_GET['idGame'])) {
    $idGame = $_GET['idGame'];
    $gamedb = $dbp->getGame($idGame);
    if ($gamedb != false) {
        $board = $gamedb['game_board_Game'];
        $boardObject = json_decode($board);
        $game = new Game($boardObject->lines, $boardObject->columns, $boardObject->deckSize, $gamedb["id_gameuser1"], $gamedb["id_gameuser2"],
            $boardObject->activePlayer, $boardObject->board, $boardObject->deckPlayer1, $boardObject->deckPlayer2);
        $res = $game->jsonSerialize();
    }
}

echo $res;
