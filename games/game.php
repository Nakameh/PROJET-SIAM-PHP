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
    echoHead("Siam - Partie", "../");
?>
<body>
    <?php
        displayBodyElements(true, $isAdmin, $username, "../");
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card rounded">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 class="card-title">Jeu</h2>
                                <h3 id="playerTurn"></h3>
                                <div id="plateau">
                                    <div class="opponent-deck">
                                        <!-- Code for the opponent's deck goes here -->
                                    </div>
                                    <div class="game-board">
                                        <div id="pawnContainer">
                                        </div>
                                    </div>
                                    <div class="player-deck">
                                        <!-- Code for the player's board goes here -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1" style="border-right: 1px solid #ccc;"></div>
                            <div class="col-md-3 overflow-auto" id="options">
                                <h2 class="card-title">Options</h2>
                                <div class="card-title">Rotation des pièces</div>
                                <br>
                                <div>
                                    <button class="btn btn-primary margin20px" id="rotateLeft"><i class="bi bi-arrow-left"></i></button>
                                    <button class="btn btn-primary margin20px" id="rotateRight"><i class="bi bi-arrow-right"></i></button>
                                </div>
                                <button class="btn btn-primary margin20px" id="confirmRotate"><i class="bi bi-check">Confirmer la rotation</i></button>
                                <hr>
                                <div class="card-title">Mouvement des pièces</div>
                                <button class="btn btn-primary margin20px" id="moveUp"><i class="bi bi-arrow-up"></i></button>
                                <div style="display: flex; align-items: center; justify-content: center;">
                                    <button class="btn btn-primary margin20px" id="moveLeft" style="flex-shrink: 1;"><i class="bi bi-arrow-left"></i></button>
                                    <img src="" alt="Une image de la pièce du joueur à faire tourner" id="rotatePicture" style="flex-shrink: 1; max-width: 100%; max-height: 100%;">
                                    <button class="btn btn-primary margin20px" id="moveRight" style="flex-shrink: 1;"><i class="bi bi-arrow-right"></i></button>
                                </div>
                                <button class="btn btn-primary margin20px" id="moveDown"><i class="bi bi-arrow-down"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/game.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let idGameTmp = <?php echo $idGame; ?>;
            let idUserTmp = <?php echo $id_user; ?>;

            idGame = idGameTmp;
            idUser = idUserTmp;

            generateContent(idGameTmp, idUserTmp);

            let intervalGameExist =setInterval(verifyGameExist, 250, idGameTmp);

            document.getElementById("confirmRotate").addEventListener("click", confirmRotate);
            
            document.getElementById("moveUp").addEventListener("click", move(0, -1));
            document.getElementById("moveDown").addEventListener("click", move(0, 1));
            document.getElementById("moveLeft").addEventListener("click", move(-1, 0));
            document.getElementById("moveRight").addEventListener("click", move(1, 0));

            setInterval(gameFinished, 250);
        });
    </script>
</body>
</html>
