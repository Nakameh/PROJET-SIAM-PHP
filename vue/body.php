<?php

function displayBodyElements(bool $isConnected, bool $isAdmin, $username) {
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"><a class="navbar-brand" href="/">
        <img src="/img/logo_sia.gif" alt="Logo SIAM" height="40" class="d-inline-block align-top">
            </a><button class="navbar-toggler nav-item" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span></button>

            <div class="collapse navbar-collapse" id="navbarNav"><div class="navbar-nav">
                    <a class="nav-item nav-link" href="/">Accueil</a>
                    <a class="nav-item nav-link" href="/rules">Règles</a>
                    <a class="nav-item nav-link" href="/games">Parties</a>
    <?php
    if ($isConnected) {
        ?>
        <a class="nav-item nav-link" href="/mygames">Mes parties</a>
        <a class="btn btn-outline-light nav-item" href="/creategame">Créer une partie</a>
        <?php
    }
    ?>
    </div><div class="navbar-nav ml-auto">

    <?php
    if ($isAdmin) {
        ?>
        <a class="btn btn-outline-light nav-item" href="/createaccount">Créer un compte</a>
        <?php
    }

    if ($isConnected) {
        ?>
        <div class="dropdown">
            <a class="btn btn-outline-light nav-item dropdown-toggle" href="#" role="button"
                id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="/img/elephantS.gif" alt="Logout" height="40">
                <?= $username ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                <li><a class="dropdown-item" href="/changepassword">Changer le mot de passe</a></li>
                <li><a class="dropdown-item" href="/login/disconnect.php">Déconnexion</a></li>
            </ul>
        </div>
        <?php
    } else
    {
        ?>
        <a class="btn btn-outline-light nav-item" href="/login">Connexion</a>
        <?php
    }
    ?>
    </div></div></nav>
    <img src="/img/background2.png" class="background-image" alt="background">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

    <?php
}
