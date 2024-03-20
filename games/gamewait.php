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
    echoHead("Siam - Partie : Attente d'un adversaire", "../");
?>
<body>
    <?php
        displayBodyElements(true, $isAdmin, $username, "../");
    ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card rounded">
                    <div class="card-body">
                        <h5 class="card-title">En attente d'un adversaire !</h5>
                        <div class="text-center">
                            <div class="spinner-border" role="status"></div>
                            <p>En attente d'un adversaire</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setInterval(function() {
                fetch(`wait.php?idGame=${<?php echo $idGame; ?>}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data === "true") {
                            window.location.href = `game.php?idGame=${<?php echo $idGame; ?>}`;
                        }
                    });
            }, 250);
        });
    </script>
</body>
</html>
