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

                            <div class="col-md-3 overflow-auto">
                                <h4>Informations</h4>
                                <div class="myOverflowY">
                                    <ol>
                                        <li>Lire les règles dans l'onglet Règles pour comprendre le fonctionnement du jeu</li>
                                        <li>Vous pouvez faire une action par tour de jeu si c'est le votre</li>
                                        <li>Pour commencer la partie si c'est votre tour, sélectionner un pion de votre deck en bas et le placer sur une des cases d'entrée en vert</li>
                                        <li>Ensuite pour manipuler cette pièces, cliquez dessus</li>
                                        <li>Vous pouvez tourner les pièces avec les flèches dans la sections rotation des pièces</li>
                                        <li>Vous pouvez déplacer les pièces avec les flèches dans la section mouvement des pièces</li>
                                        <li>Vous pouvez sortie la pièce du plateau en la faisant se déplacer en dehors du plateau</li>
                                        <li>Shift click gauche pour enter un pion à une position si il y a déjà un de vos pion</li>
                                        <li>Dans les coins, pour savoir dans quelle direction pousser :
                                            <ul>
                                                <li>Clic gauche : Pousser à la verticale</li>
                                                <li>Clic droit : Pousser à l'horizontale</li>
                                            </ul>
                                        </li>
                                        <li>Votre objectif est de finir la partie en poussant un rocher en dehors du plateau, Bonne chance !</li>
                                    </ol>
                                </div>
                                <hr>
                                <button class="btn btn-danger" id="cancelSelection">Annuler la sélection</button>
                                <hr>
                                <h4>Rotation des pièces</h4>
                                <div id="rotationDiv" class="options myHidden">
                                    <br>
                                    <div>
                                        <button class="btn btn-primary margin20px" id="rotateLeft">
                                            <img src="../img/white-arrow-28.png" alt="Une image d'une flèche incurvée" width="30" class="reverse-image">
                                        </button>
                                        <img src="" alt="Une image de la pièce du joueur à faire tourner" id="rotatePicture" style="flex-shrink: 1; max-width: 100%; max-height: 100%;">
                                        <button class="btn btn-primary margin20px" id="rotateRight">
                                            <img src="../img/white-arrow-28.png" alt="Une image d'une flèche incurvée" width="30">
                                        </button>
                                    </div>
                                    <button class="btn btn-primary margin20px myHidden" id="confirmRotate"><i class="bi bi-check">Confirmer la rotation</i></button>
                                </div>

                                <hr>
                                <h4>Mouvement des pièces</h4>

                                <div id="movementDiv" class="options myHidden">
                                    <button class="btn btn-primary margin20px" id="moveUp"><i class="bi bi-arrow-up"></i></button>
                                    <div style="display: flex; align-items: center; justify-content: center;">
                                        <button class="btn btn-primary margin20px" id="moveLeft" style="flex-shrink: 1;"><i class="bi bi-arrow-left"></i></button>
                                        <img src="" alt="Une image de la pièce du joueur à faire tourner" id="movePicture" style="flex-shrink: 1; max-width: 100%; max-height: 100%;">
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
    </div>

    <script src="../js/game.js"></script>

    <script>
        document.addEventListener("contextmenu", function(e) {
            e.preventDefault();
        });

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
