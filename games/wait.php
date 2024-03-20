<?php

session_start();
header("Content-Type: application/json");
require_once "../db/databaseProject.php";

$dbp = new DataBaseProject("../db/projet.sqlite3");

$res = "false";

if ($dbp->gameHas2Players($_GET['idGame'])) {
    $res = "true";
}

echo json_encode($res);
