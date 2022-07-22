<?php
include("class/LBTOKEN.php");
include('class/LBAPI.php');

header("Content-type: text/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if(LBTOKEN::read()){

    } else {

        http_response_code(403);

    }

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

}




?>
