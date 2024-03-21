<?php
function echoHead($title, $pathToRoot) {
    ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
                rel="stylesheet"
                integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
                crossorigin="anonymous">

        <link rel="shortcut icon" href="<?php echo $pathToRoot ?>/img/ES.gif" type="image/x-icon">
        <link rel="stylesheet" href="<?php echo $pathToRoot ?>/style/generalStyle.css">
    </head>
    <?php
}


function displayBodyElements(bool $isConnected, bool $isAdmin, $username, $pathToRoot) {
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"><a class="navbar-brand" href="<?php echo $pathToRoot ?>">
        <img src="<?php echo $pathToRoot ?>img/logo_sia.gif" alt="Logo SIAM" height="40" class="d-inline-block align-top">
            </a><button class="navbar-toggler nav-item" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span></button>

            <div class="collapse navbar-collapse" id="navbarNav"><div class="navbar-nav">
                    <a class="nav-item nav-link" href="<?php echo $pathToRoot ?>">Accueil</a>
                    <a class="nav-item nav-link" href="<?php echo $pathToRoot ?>rules/rules.php">Règles</a>
                    <a class="nav-item nav-link" href="<?php echo $pathToRoot ?>games/listgames.php">Parties</a>
    <?php
    if ($isConnected) {
        ?>
        <a class="nav-item nav-link" href="<?php echo $pathToRoot ?>games/mygameslist.php">Mes parties</a>
        <a class="btn btn-outline-light nav-item" href="<?php echo $pathToRoot ?>games/gamecreate.php">Créer une partie</a>
        <?php
    }
    ?>
    </div><div class="navbar-nav ml-auto">

    <?php
    if ($isAdmin) {
        ?>
        <a class="btn btn-outline-light nav-item" href="<?php echo $pathToRoot ?>games/gamedeletelist.php">Supprimer une partie</a>
        <a class="btn btn-outline-light nav-item" href="<?php echo $pathToRoot ?>login/createaccount.php">Créer un compte</a>
        <?php
    }

    if ($isConnected) {
        ?>
        <div class="dropdown">
            <a class="btn btn-outline-light nav-item dropdown-toggle" href="#" role="button"
                id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?php echo $pathToRoot ?>img/ES.gif" alt="Logout" height="40">
                <?= $username ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                <li><a class="dropdown-item" href="<?php echo $pathToRoot ?>login/changepassword.php">Changer le mot de passe</a></li>
                <li><a class="dropdown-item" href="<?php echo $pathToRoot ?>login/disconnect.php">Déconnexion</a></li>
            </ul>
        </div>
        <?php
    } else
    {
        ?>
        <a class="btn btn-outline-light nav-item" href="<?php echo $pathToRoot ?>login/login.php">Connexion</a>
        <?php
    }
    ?>
    </div></div></nav>
    <img src="<?php echo $pathToRoot ?>img/background2.png" class="background-image" alt="background">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

    <?php
}
