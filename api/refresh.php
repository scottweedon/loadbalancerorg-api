<?php

header("Content-type: text/json");

//include "class/jwt.php";
include "class/LBTOKEN.php";
include "class/LBAPI.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = new LBTOKEN();
    if($token->refresh(LBAPI::getAuthorizationHeader())){

        echo json_encode($token);

    } else {

        //echo "failed";
        http_response_code(403);

    }

}

?>