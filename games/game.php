<?php
require_once "../vue/head.php";
require_once "../vue/body.php";
require_once "../db/databaseProject.php";
require_once "gameClass.php";

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
    $dbp->updateDateLastSeen($id_user);
}

if (isset($_GET['idGame'])) {
    $idGame = $_GET['idGame'];
    $game = $dbp->getGame($idGame);
    if ($game == false) {
        header("Location: ../index.php");
        exit();
    }
    if ($game['id_gameuser1'] != $id_user && $game['id_gameuser2'] != $id_user) {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}




?>
<!DOCTYPE html>
<html lang="en">
<?php
    echoHead("Siam - Partie");
?>
<body>
    <?php
        displayBodyElements(true, $isAdmin, $username);
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card rounded">
                    <h2 class="card-header">Liste des mouvements</h2>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card rounded">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 class="card-title">Jeu</h2>
                                <div id="plateau">
                                    <div class="opponent-deck margin20px">
                                        <!-- Code for the opponent's deck goes here -->
                                    </div>
                                    <div class="game-board">
                                        <div id="pawnContainer">
                                        </div>
                                    </div>
                                    <div class="player-deck margin20px">
                                        <!-- Code for the player's board goes here -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1" style="border-right: 1px solid #ccc;"></div>
                            <div class="col-md-3">
                                <h2 class="card-title">Options</h2>
                                <!-- Code for the options goes here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .game-board{
            background-image: url("/img/plateau.jpg");
            background-size: cover;
            width: 440px;
            height: 440px;
        }

        .cardDeck{
            border: 1px solid #000000;
            margin: auto 5px;
            height: 80px;
            width: 80px;
            display: flex;
            
        }

        .cardDeck:hover{
            border: 2px solid #00ff00;
            height: 100px;
            width: 100px;
        }

        #pawnContainer{
            margin: 20px;
            padding-top: 20px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-template-rows: repeat(5, 1fr);
        }
    </style>

    <script src="game.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let idGame = <?php echo $idGame; ?>;
            let idUser = <?php echo $id_user; ?>;

            generateContent(idGame, idUser);
        });
    </script>
</body>
</html>
