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
    private static string $empty = " ";
    private static string $rock = "ROCK";
    private int $idWinner;


    public function __construct(int $lines, int $columns, int $deckSize, int $player1, int $player2,
                int $activePlayer, array $board = null, array $deckPlayer1 = null, array $deckPlayer2 = null)
    {
        $this->idWinner = -1;
        $this->lines = $lines;
        $this->columns = $columns;
        $this->deckSize = $deckSize;
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->activePlayer = $activePlayer;
        if ($board == null) {
            $this->board = array_fill(0, $lines, array_fill(0, $columns, self::$empty));
            $this->board[2][1] = self::$rock;
            $this->board[2][2] = self::$rock;
            $this->board[2][3] = self::$rock;
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

    public function getBoard() : array
    {
        return $this->board;
    }

    public function boardCaseIsFree($line, $colonne) {
        return $this->board[$line][$colonne] == self::$empty;
    }

    public function getUserPlayingId() {
        if ($this->activePlayer == 0) {
            return $this->player1;
        }
        return $this->player2;
    }

    public function addPawn($line, $column, $pawn, $rotation, $indexDeck, $id_user, $pushDirection) {
        if ($line != 0 && $line != 4 && $column != 0 && $column != 4) {
            return false;
        }

        if ($this->getUserDeckIndexEmpty($id_user, $indexDeck)) {
            return false;
        }

        if ($this->boardCaseIsFree($line, $column)) {
            $this->board[$line][$column] = $pawn.$rotation;
    
            if ($id_user == $this->player1) {
                $this->deckPlayer1[$indexDeck] = self::$empty;
            } else {
                $this->deckPlayer2[$indexDeck] = self::$empty;
            }
            $this->nextUserPlaying();

        } else {
            $pasX = 0;
            $pasY = 0;
            if ($line == 0) {
                $pasY = 1;
            } elseif ($line == 4) {
                $pasY = -1;
            } elseif ($column == 0) {
                $pasX = 1;
            } elseif ($column == 4) {
                $pasX = -1;
            }
            
            if ($line == 0 && $column ==0) {
                if ($pushDirection == "H") {
                    $pasX = 1;
                    $pasY = 0;
                } else {
                    $pasX = 0;
                    $pasY = 1;
                }
            } elseif ($line == 0 && $column == 4) {
                if ($pushDirection == "H") {
                    $pasX = -1;
                    $pasY = 0;
                } else {
                    $pasX = 0;
                    $pasY = 1;
                }
            } elseif ($line == 4 && $column == 0) {
                if ($pushDirection == "H") {
                    $pasX = 1;
                    $pasY = 0;
                } else {
                    $pasX = 0;
                    $pasY = -1;
                }
            } elseif ($line == 4 && $column == 4) {
                if ($pushDirection == "H") {
                    $pasX = -1;
                    $pasY = 0;
                } else {
                    $pasX = 0;
                    $pasY = -1;
                }
            }

            $res = $this->movePawn($line, $column, $pasX, $pasY, $this->board[$line][$column][0], $this->board[$line][$column][1], $id_user, 1);
            if (!$res) {
                return false;
            }
            $this->board[$line][$column] = $pawn.$rotation;
    
            if ($id_user == $this->player1) {
                $this->deckPlayer1[$indexDeck] = self::$empty;
            } else {
                $this->deckPlayer2[$indexDeck] = self::$empty;
            }
        }
        
        return true;
    }

    public function getUserDeckIndexEmpty(int $id_user, int $index):bool {
        if ($index < 0 || $index > 4) {
            return false;
        }
        if ($id_user == $this->player1) {
            return $this->deckPlayer1[$index] == self::$empty;
        }
        return $this->deckPlayer2[$index] == self::$empty;
    }

    public function rotatePawn($line, $column, $pawn, $rotation):bool {
        if ($line > 4 || $line < 0 || $column > 4 || $column < 0) {
            return false;
        }
        if ($this->board[$line][$column][0] != $pawn) {
            return false;
        }
        if ($pawn != self::$player1Pawns && $pawn != self::$player2Pawns) {
            return false;
        }
        if ($this->board[$line][$column] == $pawn.$rotation) {
            return false;
        }
        $this->board[$line][$column] = $pawn.$rotation;

        $this->nextUserPlaying();

        return true;
    }


    private function nextUserPlaying() {
        $this->activePlayer = ($this->activePlayer + 1) % 2;
    }

    public function movePawn($line, $column, $pasX, $pasY, $pawn, $rotation, $id_user, $nbAlly = 0) {
        $newLine = $line + $pasY;
        $newColumn = $column + $pasX;

        if ($line < 0 || $line > 4 || $column < 0 || $column > 4) {
            return false;
        }

        $this->board[$line][$column] = $pawn.$rotation;

        if ($newLine < 0 || $newLine > 4 || $newColumn < 0 || $newColumn > 4) {
            return $this->recoverPawn($line, $column, $pawn, $id_user);
        }
        if ($this->board[$newLine][$newColumn] != self::$empty) {
            return $this->pushObstacle($line, $column, $pasX, $pasY, $pawn, $rotation, $id_user, $nbAlly);
        }

        $this->board[$newLine][$newColumn] = $pawn.$rotation;
        $this->board[$line][$column] = self::$empty;

        $this->rotatePawn($newLine, $newColumn, $pawn, $rotation);

        $this->nextUserPlaying();

        return true;
    }


    private function recoverPawn($line, $column, $pawn, $id_user) : bool {
        if ($this->board[$line][$column][0] != $pawn) {
            return false;
        }
        $myDeck = $id_user == $this->player1 ? $this->deckPlayer1 : $this->deckPlayer2;

        for ($i = 0; $i < $this->deckSize; $i++) {
            if ($myDeck[$i] == self::$empty) {
                if ($id_user == $this->player1) {
                    $this->deckPlayer1[$i] = $pawn;
                } else {
                    $this->deckPlayer2[$i] = $pawn;
                }
                $this->board[$line][$column] = self::$empty;
                $this->nextUserPlaying();
                return true;
            }
        }
        return false;
    }

    private function getDirectionFromPas($pasX, $pasY) {
        if ($pasX == 0 && $pasY == -1) {
            return "N";
        }
        if ($pasX == 0 && $pasY == 1) {
            return "S";
        }
        if ($pasX == 1 && $pasY == 0) {
            return "E";
        }
        if ($pasX == -1 && $pasY == 0) {
            return "W";
        }
        return "";
    }

    private function getReverseDirection($direction) {
        if ($direction == "N") {
            return "S";
        }
        if ($direction == "S") {
            return "N";
        }
        if ($direction == "W") {
            return "E";
        }
        if ($direction == "E") {
            return "W";
        }
        return "";
    }



    public function canPushObstacle($line, $column, $pasX, $pasY, $pawn, $rotation, $na = 0) {
        $direction = $this->getDirectionFromPas($pasX, $pasY);
        $reverseDirection = $this->getReverseDirection($direction);

        if ($direction == "") {
            return false;
        }

        if ($rotation != $direction) {
            return false;
        }

        $newline = $line;
        $newcolumn = $column;
        $nbAlly = $na;
        $nbOpponent = 0;
        $nbRock = 0;

        while ($newline >= 0 && $newline < 5 && $newcolumn >= 0 && $newcolumn < 5 && $this->board[$newline][$newcolumn] != self::$empty) {
            if ($this->board[$newline][$newcolumn][1] == $direction) {
                $nbAlly++;
            } elseif ($this->board[$newline][$newcolumn][1] == $reverseDirection) {
                $nbOpponent++;
            } elseif ($this->board[$newline][$newcolumn] == self::$rock) {
                $nbRock++;
            }

            $newline += $pasY;
            $newcolumn += $pasX;
        }
        if ($nbAlly == $nbOpponent) {
            return false;
        }

        return $nbAlly - $nbOpponent - $nbRock >= 0;
    }

    private function pushObstacle($line, $column, $pasX, $pasY, $pawn, $rotation, $id_user, $nbAlly = 0) {
        if (!$this->canPushObstacle($line, $column, $pasX, $pasY, $pawn, $rotation, $nbAlly)) {
            return false;
        }

        $newline = $line;
        $newcolumn = $column;
        $value = self::$empty;

        while ($newline >= 0 && $newline < 5 && $newcolumn >= 0 && $newcolumn < 5 && $this->board[$newline][$newcolumn] != self::$empty) {
            $tmpValue = $this->board[$newline][$newcolumn];
            $this->board[$newline][$newcolumn] = $value;
            $value = $tmpValue;
            $newline += $pasY;
            $newcolumn += $pasX;
        }

        if ($value != self::$empty) {
            if ($newline >= 0 && $newline < 5 && $newcolumn >= 0 && $newcolumn < 5) {
                $this->board[$newline][$newcolumn] = $value;
            } else {
                if ($value == self::$rock) {
                    $this->idWinner = $id_user;
                    $this->nextUserPlaying();
                    return true;
                } else {
                    if ($value[0] == self::$player1Pawns) {
                        for ($i = 0; $i < $this->deckSize; $i++) {
                            if ($this->deckPlayer1[$i] == self::$empty) {
                                $this->deckPlayer1[$i] = $value[0];
                                break;
                            }
                        }
                    } elseif ($value[0] == self::$player2Pawns) {
                        for ($i = 0; $i < $this->deckSize; $i++) {
                            if ($this->deckPlayer2[$i] == self::$empty) {
                                $this->deckPlayer2[$i] = $value[0];
                                break;
                            }
                        }
                    }
                }
            }

        }
        $this->nextUserPlaying();
        return true;
    }


    public function getWinner() {
        return $this->idWinner;
    }


}
