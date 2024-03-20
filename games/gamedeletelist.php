<?php
require_once "../vue/head.php";
require_once "../vue/body.php";
require_once "../db/databaseProject.php";

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
    if (!$isAdmin) {
        header("Location: ../index.php");
        exit();
    }
    $dbp->updateDateLastSeen($id_user);
}

$games = $dbp->getGames();


?>
<!DOCTYPE html>
<html lang="en">
<?php
    echoHead("Siam - Suppresion de partie", "../");
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
                        <h5 class="card-title">Suppression de partie</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <caption>Parties en cours :</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre de joueurs</th>
                                        <th scope="col">User 1</th>
                                        <th scope="col">User 2</th>
                                        <th scope="col">Nombre de tours</th>
                                        <th scope="col">Date de création</th>
                                        <th scope="col">Supprimer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($games as $game) {
                                            ?>
                                            <tr>
                                                <?php
                                                    if (!empty($game["id_gameuser2"])) {
                                                        ?>
                                                        <td class="text-center">2/2</td>
                                                        <td class="text-center"><?php echo $dbp->getUsername($game["id_gameuser1"]); ?></td>
                                                        <td class="text-center"><?php echo $dbp->getUsername($game["id_gameuser2"]); ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td class="text-center">1/2</td>
                                                        <td class="text-center"><?php echo $dbp->getUsername($game["id_gameuser1"]); ?></td>
                                                        <td class="text-center">-</td>
                                                        <?php
                                                    }
                                                    ?>
                                                    <td class="text-center"><?php echo $game["nb_turn_Game"]; ?></td>
                                                    <td class="text-center"><?php echo $game["date_debut_Game"]; ?></td>
                                                    <td><a href="gamedelete.php?idGame=<?php echo $game['id_Game']; ?>" class="btn btn-danger">Supprimer</a></td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
