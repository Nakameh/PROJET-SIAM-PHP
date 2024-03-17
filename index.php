<?php
require_once "vue/head.php";
require_once "vue/body.php";
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

?>

<!DOCTYPE html>
<html lang="en">
<?php
    echoHead("Siam - Accueil");
?>
<body>
    <?php
        displayBodyElements($isConnected, $isAdmin , $username);
    ?>
    <div class="container">
        <div class="alert alert-danger" role="alert" id="error" style="display: none;">
            <strong><?php echo $isAdmin ?></strong> <span id="errorText"></span>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h2 class="card-title">Vos parties</h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group list-group-item">Partie 1</li>
                            <li class="list-group list-group-item">Partie 2</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h2 class="card-title">Parties en cours</h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group list-group-item">Partie 1</li>
                            <li class="list-group list-group-item">Partie 2</li>
                        </ul>
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
