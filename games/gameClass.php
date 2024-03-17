<?php

class Game implements JsonSerializable
{
    private array $board;
    private int $player1;
    private int $player2;
    private int $activePlayer;
    private array $deckPlayer1;
    private array $deckPlayer2;
    private int $lines;
    private int $columns;
    private int $deckSize;
    private static string $player1Pawns = "E";
    private static string $player2Pawns = "R";
    private static string $south = "S";
    private static string $north = "N";
    private static string $east = "E";
    private static string $west = "W";
    private static string $empty = " ";

    public function __construct(int $lines, int $columns, int $deckSize, int $player1, int $player2,
                int $activePlayer, array $board = null, array $deckPlayer1 = null, array $deckPlayer2 = null)
    {
        $this->lines = $lines;
        $this->columns = $columns;
        $this->deckSize = $deckSize;
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->activePlayer = $activePlayer;
        if ($board == null) {
            $this->board = array_fill(0, $lines, array_fill(0, $columns, self::$empty));
        } else {
            $this->board = $board;
        }
        if ($deckPlayer1 == null) {
            $this->deckPlayer1 = array_fill(0, $deckSize, self::$player1Pawns);
        } else {
            $this->deckPlayer1 = $deckPlayer1;
        }
        if ($deckPlayer2 == null) {
            $this->deckPlayer2 = array_fill(0, $deckSize, self::$player2Pawns);
        } else {
            $this->deckPlayer2 = $deckPlayer2;
        }
    }
    
    public function jsonSerialize() : string
    {
        return json_encode(get_object_vars($this));
    }
}
