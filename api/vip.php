<?php
include("class/LBTOKEN.php");
include('class/LBAPI.php');

header("Content-type: text/json");

try {

    $data = file_get_contents("php://input");

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        if(LBTOKEN::read()){

            $exists = false;
            $config = json_decode(LBAPICLI::getconfig());

            foreach($config->L4_vips as $vip){
                if($vip->vip == $_GET['name']){
                    $exists = true;
                    echo json_encode($vip);
                }
            }

            foreach($config->L7_vips as $vip){
                if($vip->vip == $_GET['name']){
                    $exists = true;
                    echo json_encode($vip);
                }
            }
            
            if(!$exists){
                LBAPI::print_response("Get VIP", 404);
            }

        } else {

            LBAPI::print_response("Get VIP", 401);

        }

    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if(LBTOKEN::read()){

            $vip = json_decode($data);

            if($vip->layer == 7){

                $obj = new L7LBAPIVIP($vip);
                LBAPI::print_response("Add VIP", $obj->add());

            } else if($vip->layer == 4) {

                $obj = new L4LBAPIVIP($vip);
                LBAPI::print_response("Add VIP", $obj->add());

            } else {

                LBAPI::print_response("Add VIP", 400);

            }
        
        } else {

            LBAPI::print_response("Add VIP", 401);

        }

    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

        if(LBTOKEN::read()){

            $vip = json_decode($data);

                $obj = new LBAPIVIP($vip);
                LBAPI::print_response("Delete VIP", $obj->delete());

            
        
        } else {

            LBAPI::print_response("Delete VIP", 401);

        }

    }

    if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

        if(LBTOKEN::read()){

            $vip = json_decode($data);

            if($vip->layer == 7){

                $obj = new L7LBAPIVIP($vip);
                LBAPI::print_response("Edit VIP", $obj->edit());

            } else if($vip->layer == 4) {

                $obj = new L4LBAPIVIP($vip);
                LBAPI::print_response("Edit VIP", $obj->edit());

            } else {

                LBAPI::print_response("Edit VIP", 400);

            }
        
        } else {

            LBAPI::print_response("Edit VIP", 401);

        }


    }

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

        
            $vips = array();
            $vips['L4_vips'] = array();
            $vips['L7_vips'] = array();

            $vips['L4_vips'][0] = new L4LBAPIVIP(null, true);
            $vips['L7_vips'][0] = new L7LBAPIVIP(null, true);

            echo json_encode($vips);

    }

} catch (Exception $e) {

    LBAPI::logger(json_encode($e));

}

?>