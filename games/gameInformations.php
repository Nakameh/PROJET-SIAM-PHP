<?php
require_once "../db/databaseProject.php";
require_once "gameClass.php";


header("Content-Type: application/javascript");

$res = "";
$dbp = new DataBaseProject("../db/projet.sqlite3");

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
