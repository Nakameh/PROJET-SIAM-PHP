<?php
require_once "../vue/head.php";
require_once "../vue/body.php";
require_once "../db/databaseProject.php";
require_once "gameClass.php";

session_start();
$dbp = new DataBaseProject("../db/projet.sqlite3");

header("Content-Type: application/json");



if (!isset($_GET['idGame'])) {
    echo "false";
    exit();
}
$id_game = $_GET['idGame'];


$action = "";
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    echo "false";
    exit();
}


if (!isset($_GET['userId'])) {
    echo "false";
    exit();
}
$id_user = $_GET['userId'];


$gamedb = $dbp->getGame($id_game);
if (!$gamedb) {
    echo "false";
    exit();
}

$board = $gamedb['game_board_Game'];
$boardObject = json_decode($board);

$game = new Game($boardObject->lines, $boardObject->columns, $boardObject->deckSize, $gamedb["id_gameuser1"], $gamedb["id_gameuser2"],
    $boardObject->activePlayer, $boardObject->board, $boardObject->deckPlayer1, $boardObject->deckPlayer2);


if ($game->getUserPlayingId() != $id_user) {
    echo "false";
    exit();
}



if ($action == "addPawn") {
    $line = "";
    $column = "";
    if (isset($_GET['line'])) {
        $line = $_GET['line'];
    } else {
        echo "false";
        exit();
    }
    if (isset($_GET['column'])) {
        $column = $_GET['column'];
    } else {
        echo "false";
        exit();
    }

    if (isset($_GET['pawn'])) {
        $pawn = $_GET['pawn'];
    } else {
        echo "false";
        exit();
    }

    if (isset($_GET['rotation'])) {
        $rotation = $_GET['rotation'];
    } else {
        echo "false";
        exit();
    }

    if (isset($_GET['indexDeck'])) {
        $indexDeck = $_GET['indexDeck'];
    } else {
        echo 'false';
        exit();
    }


    if ($line != 0 && $line != 4 && $column != 0 && $column != 4) {
        echo "false";
        exit();
    }


    if (! $game->boardCaseIsFree($line, $column)){
        echo "false";
        exit();
    }

    if ($game->getUserDeckIndexEmpty($id_user, $indexDeck)) {
        echo "false";
        exit();
    }


    $game->addPawn($line, $column, $pawn.$rotation, $indexDeck, $id_user);
    $dbp->updateBoard($id_game, $game->jsonSerialize());

    echo "true";
    exit();
}


if ($action == "rotatePawn") {
    $line = "";
    $column = "";
    $rotation = "S";
    $pawn = "";
    if (isset($_GET['line'])) {
        $line = $_GET['line'];
    } else {
        echo "false";
        exit();
    }
    if (isset($_GET['column'])) {
        $column = $_GET['column'];
    } else {
        echo "false";
        exit();
    }
    if (isset($_GET['rotation'])) {
        $rotation = $_GET['rotation'];
    } else {
        echo "false";
        exit();
    }
    if (isset($_GET['pawn'])) {
        $pawn = $_GET['pawn'];
    } else {
        echo "false";
        exit();
    }


    $ret = $game->rotatePawn($line, $column, $pawn, $rotation);
    if (!$ret) {
        echo "false";
        exit();
    }

    $dbp->updateBoard($id_game, $game->jsonSerialize());

    echo "true";
    exit();
}

if ($action == "movePawn") {
    $line = "";
    $column = "";
    $pasX = "";
    $pasY = "";
    $pawn = "";
    $rotation = "S";

    if (isset($_GET['line'])) {
        $line = $_GET['line'];
    } else {
        echo "false";
        exit();
    }
    if (isset($_GET['column'])) {
        $column = $_GET['column'];
    } else {
        echo "false";
        exit();
    }
    if (isset($_GET['pasX'])) {
        $pasX = $_GET['pasX'];
    } else {
        echo "false";
        exit();
    }
    if (isset($_GET['pasY'])) {
        $pasY = $_GET['pasY'];
    } else {
        echo "false";
        exit();
    }
    if (isset($_GET['pawn'])) {
        $pawn = $_GET['pawn'];
    } else {
        echo "false";
        exit();
    }
    if (isset($_GET['rotation'])) {
        $rotation = $_GET['rotation'];
    } else {
        echo "false";
        exit();
    }

    $ret = $game->movePawn($line, $column, $pasX, $pasY, $pawn, $rotation, $id_user);
    if (!$ret) {
        echo "false";
        exit();
    }

    $dbp->updateBoard($id_game, $game->jsonSerialize());

    if ($game->getWinner() != -1) {
        $dbp->setIdWinner($id_game, $game->getWinner());
    }

    echo "true";
    exit();
}


echo "false";
exit();
