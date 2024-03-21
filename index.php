<?php
require_once "vue/vue.php";

require_once "db/databaseProject.php";

session_start();

$dbp = new DataBaseProject("db/projet.sqlite3");

$isConnected = false;
$isAdmin = false;
$username ="";

if (isset($_SESSION['id_user'])) {
    if (!$dbp->idUserExist($_SESSION['id_user'])) {
        header("Location: login/disconnect.php");
        exit();
    }
    $isConnected = true;
    $isAdmin = $dbp->isAdmin($_SESSION['id_user']);
    $username = $dbp->getUsername($_SESSION['id_user']);
    $dbp->updateDateLastSeen($_SESSION['id_user']);
}

$activesUsers = $dbp->getActivesUsers();

$listGames = $dbp->getListGamesWith1Player();

if ($isConnected) {
    $myGames = $dbp->getMyGames($_SESSION['id_user']);
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
    echoHead("Siam - Accueil", "./");
?>
<body>
    <?php
        displayBodyElements($isConnected, $isAdmin , $username, "./");
    ?>
    <div class="container">
        <div class="alert alert-danger" role="alert" id="error" style="display: none;">
            <strong><?php echo $isAdmin ?></strong> <span id="errorText"></span>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Liste de mes parties :</h5>
                        <div class="table-responsive">
                            <table class="table table-striped" style="width : 0px;">
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
                                        if ($isConnected)
                                        {
                                            foreach ($myGames as $game) {
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
                                                    if ($game["id_gameuser1"] == $_SESSION['id_user']) {
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
                                                    <td class="text-center"><a href="games/gamejoin.php?idGame=<?php echo $game["id_Game"]; ?>" class="btn btn-primary">Rejoindre</a></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Liste des parties en attente de joueurs :</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <caption>Parties en attente de joueurs</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre de joueurs</th>
                                        <th scope="col">Créateur</th>
                                        <th scope="col">Date de création</th>
                                        <th scope="col">Rejoindre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($listGames as $game) {
                                            ?>
                                            <tr>
                                                <td class="text-center">1/2</td>
                                                <td class="text-center"><?php echo $dbp->getUsername($game["id_gameuser1"]); ?></td>
                                                <td class="text-center"><?php echo $game["date_debut_Game"]; ?></td>
                                                <td class="text-center"><a href="games/gamejoin.php?idGame=<?php echo $game["id_Game"]; ?>" class="btn btn-primary">Rejoindre</a></td>
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

            <div class="col-sm-12 col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h2 class="card-title">Joueurs connectés</h2>
                        <ul class="list-group list-group-flush">
                            <?php
                            foreach ($activesUsers as $user) {
                                echo "<li class=\"list-group list-group-item\">".$user["username_User"]."</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
