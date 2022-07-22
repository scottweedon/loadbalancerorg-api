<?php

header("Content-type: text/json");

//include "class/jwt.php";
include "../class/LBTOKEN.php";
include "../class/LBAPI.php";


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $user = "test";

    $token = new LBTOKEN();
    $token->create($user);
    echo json_encode($token);

}

?>