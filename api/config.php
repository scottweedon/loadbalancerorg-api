<?php
include("class/LBTOKEN.php");
include('class/LBAPI.php');

header("Content-type: text/json");

try {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        if(LBTOKEN::read()){

            $config = LBAPICLI::getconfig($_GET['raw']);
            echo $config;

        } else {

            http_response_code(403);

        }

    }

} catch (Exception $e) {

    LBAPI::logger(json_encode($e));

}

?>