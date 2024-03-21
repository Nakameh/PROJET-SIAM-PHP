<?php
require_once "../vue/vue.php";

require_once "../db/databaseProject.php";

session_start();

$dbp = new DataBaseProject("../db/projet.sqlite3");
$isAdmin = false;
$similarPassword = false;
$incorrectPassword = false;
$newPasswordNotSimilar = false;

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit();
} else {
    if (!$dbp->idUserExist($_SESSION['id_user'])) {
        header("Location: disconnect.php");
        exit();
    }
    $id_user = $_SESSION['id_user'];
    $username = $dbp->getUsername($id_user);
    $isAdmin = $dbp->isAdmin($id_user);
    $dbp->updateDateLastSeen($id_user);
}

if (isset($_POST['oldpassword']) && isset($_POST['password']) && isset($_POST['password2'])) {
    $oldpassword = $_POST['oldpassword'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password == $password2) {
        $hash = $dbp->getHash($id_user);
        if (password_verify($oldpassword, $hash)) {
            if ($oldpassword != $password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $dbp->updatePassword($id_user, $hash);
                header("Location: ../index.php");
                exit();
            } else {
                $similarPassword = true;
            }
        } else {
            $incorrectPassword = true;
        }
    } else {
        $newPasswordNotSimilar = true;
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<?php
    echoHead("SIAM - Chanement de mot de passe", "../");
?>
<body>
    <?php
        displayBodyElements(true, $isAdmin, $username, "../");
    ?>
    <div class="container">
        <?php
            if ($similarPassword) {
                ?>
                <div class='alert alert-danger' role='alert'>Mot de passe similaire !</div>
                <?php
            }
            if ($incorrectPassword) {
                ?>
                <div class='alert alert-danger' role='alert'>Ancien mot de passe incorrect !</div>
                <?php
            }
            if ($newPasswordNotSimilar) {
                ?>
                <div class='alert alert-danger' role='alert'>Les nouveaux mots de passe ne sont pas similaires !</div>
                <?php
            }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card rounded">
                    <div class="card-body">
                        <h5 class="card-title">Changement de mot de passe</h5>
                        <form method="POST">
                            <div class="mb-3 margin20px">
                                <label for="oldpassword" class="form-label">Ancien mot de passe</label>
                                <input type="password" class="form-control" id="oldpassword"
                                        name="oldpassword" required>
                            </div>
                            <div class="mb-3 margin20px">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 margin20px">
                                <label for="password2" class="form-label mb-3">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="password2" name="password2" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
