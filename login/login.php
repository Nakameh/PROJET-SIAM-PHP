<?php
require_once "../vue/vue.php";

require_once "../db/databaseProject.php";

session_start();

$dbp = new DataBaseProject("../db/projet.sqlite3");
$isConnected = false;
$isAdmin = false;
$userExist = true;
$username = "";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($dbp->userExist($username)) {
        $id_user = $dbp->getUserId($username);
        $hash = $dbp->getHash($id_user);
        if (password_verify($password, $hash)) {
            $_SESSION['id_user'] = $id_user;
            $isConnected = true;
            $dbp->updateDateLastSeen($id_user);
        } else {
            $userExist = false;
        }
    } else {
        $userExist = false;
    }

}





if (isset($_SESSION['id_user'])) {
    if (!$dbp->idUserExist($_SESSION['id_user'])) {
        header("Location: disconnect.php");
        exit();
    }
    $isConnected = true;
}

if ($isConnected) {
    header("Location: ../index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<?php
    echoHead("Siam - Connexion", "../");
?>
<body>
    <?php
        displayBodyElements($isConnected, $isAdmin, $username, "../");
    ?>
    <div class="container">
        <?php
            if (!$userExist) {
                ?>

                <div class='alert alert-danger' role='alert'>Nom d'utilisateur ou mot de passe incorrect</div>
                <?php
            }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card rounded">
                    <div class="card-body">
                        <h5 class="card-title">Connexion</h5>
                        <form method="POST">
                            <div class="form-group margin20px">
                                <label for="username">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username" required
                                    <?php if(isset($_POST['username'])) {echo "value='".$_POST['username']."'";} ?>>
                            </div>
                            <div class="form-group margin20px">
                                <label for="password">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Connexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
