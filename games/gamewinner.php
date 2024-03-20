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

$gameWinner = $dbp->getIdWinner($idGame);

if ($gameWinner == false) {
    header("Location: ../index.php");
    exit();
}

$winner = $dbp->getUsername($gameWinner);

?>
<!DOCTYPE html>
<html lang="en">
<?php
    echoHead("Siam - Fin de Partie", "../");
?>
<body>
    <?php
        displayBodyElements(true, $isAdmin, $username, "../");
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card rounded text-center">
                    <h2 class="card-header">Le gagnant de la partie est : </h2>
                    <h1><?php echo $winner; ?></h1>
                </div>
            </div>
        </div>
    </div>
</body>
