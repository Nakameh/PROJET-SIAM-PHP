<?php
require_once "../vue/head.php";
require_once "../vue/body.php";
require_once "../db/databaseProject.php";

session_start();

$dbp = new DataBaseProject("../db/projet.sqlite3");
$isAdmin = false;
$isConnected = false;

if (isset($_SESSION['id_user'])) {
    if (!$dbp->idUserExist($_SESSION['id_user'])) {
        header("Location: ../login/disconnect.php");
        exit();
    }
    $id_user = $_SESSION['id_user'];
    $username = $dbp->getUsername($id_user);
    $isAdmin = $dbp->isAdmin($id_user);
    $dbp->updateDateLastSeen($id_user);
    $isConnected = true;
}

$listGames = $dbp->getMyGames($id_user);



?>
<!DOCTYPE html>
<html lang="en">
<?php
    echoHead("Siam - Parties", "../");
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
                        <h5 class="card-title">Liste de mes parties :</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <caption>Mes parties </caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre de joueurs</th>
                                        <th scope="col">Adversaire</th>
                                        <th scope="col">Date de création</th>
                                        <th scope="col">Rejoindre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($listGames as $game) {
                                            ?>
                                            <tr>
                                                <?php
                                                    if (!empty($game["id_gameuser2"])) {
                                                        ?>
                                                        <td class="text-center">2/2</td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td class="text-center">1/2</td>
                                                        <?php
                                                    }
                                                if ($game["id_gameuser1"] == $id_user) {
                                                    ?>
                                                    <td class="text-center"><?php echo $dbp->getUsername($game["id_gameuser2"]); ?></td>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <td class="text-center"><?php echo $dbp->getUsername($game["id_gameuser1"]); ?></td>
                                                    <?php
                                                }
                                                ?>
                                                <td class="text-center"><?php echo $game["date_debut_Game"]; ?></td>
                                                <td class="text-center"><a href="gamejoin.php?idGame=<?php echo $game["id_Game"]; ?>" class="btn btn-primary">Rejoindre</a></td>
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
