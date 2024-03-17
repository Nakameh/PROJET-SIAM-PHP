<?php
require_once "../vue/head.php";
require_once "../vue/body.php";
require_once "../db/databaseProject.php";

session_start();

$dbp = new DataBaseProject("../db/projet.sqlite3");
$isAdmin = false;
$usernameAlreadyExists = false;
$passwordNotSimilar = false;


if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit();
} else {
    $id_user = $_SESSION['id_user'];
    $username = $dbp->getUsername($id_user);
    $isAdmin = $dbp->isAdmin($id_user);
    if (!$isAdmin) {
        header("Location: ../index.php");
        exit();
    }
    $dbp->updateDateLastSeen($id_user);
}

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password2'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password == $password2) {
        if (!$dbp->userExist($username)) {
            $dbp->createUser($username, 0, password_hash($password, PASSWORD_DEFAULT));
            header("Location: ../index.php");
            exit();
        } else {
            $usernameAlreadyExists = true;
        }
    } else {
        $passwordNotSimilar = true;
    }
}





?>
<!DOCTYPE html>
<html lang="en">
<?php
    echoHead("Siam - Création de compte");
?>
<body>
    <?php
        displayBodyElements(true, $isAdmin, $username);
    ?>
    <div class="container">
        <?php
            if ($usernameAlreadyExists) {
                echo "<div class='alert alert-danger' role='alert'>Le nom d'utilisateur existe déjà</div>";
            }
            if ($passwordNotSimilar) {
                echo "<div class='alert alert-danger' role='alert'>Les mots de passe ne sont pas identiques</div>";
            }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card rounded">
                    <div class="card-body">
                        <h5 class="card-title">Création de compte</h5>
                        <form method="POST">
                            <div class="mb-3 margin20px">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username"
                                        name="username" required>
                            </div>
                            <div class="mb-3 margin20px">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 margin20px">
                                <label for="password2" class="form-label mb-3">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="password2" name="password2" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Créer le compte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
