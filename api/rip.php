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

            if(isset($_GET['name'])){

                foreach($config->L4_vips as $vip){
                    foreach($vip->rips as $rip){
                        if($rip->rip == $_GET['name']){
                            $exists = true;
                            echo json_encode($rip);
                        }
                    }
                }

                foreach($config->L7_vips as $vip){
                    foreach($vip->rips as $rip){
                        if($rip->rip == $_GET['name']){
                            $exists = true;
                            echo json_encode($rip);
                        }
                    }
                }

            } else {

                $rips = array();

                foreach($config->L4_vips as $vip){
                    foreach($vip->rips as $rip){
                        $exists = true;
                        array_push($rips, $rip);
                    }
                }

                foreach($config->L7_vips as $vip){
                    foreach($vip->rips as $rip){
                        $exists = true;
                        array_push($rips, $rip);
                    }
                }

                echo json_encode($rips);

            }
            
            if(!$exists){
                LBAPI::print_response("Get RIP", 404);
            }

        } else {

            LBAPI::print_response("Get RIP", 401);

        }

    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if(LBTOKEN::read()){

            $json = json_decode($data);
            $vip = json_decode('{ "vip": "' . $json->vip . '"}');
            $obj = new LBAPIRIP($vip, $json);
            LBAPI::print_response("Add RIP", $obj->add());
        
        } else {

            LBAPI::print_response("Add RIP", 401);

        }

    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

        if(LBTOKEN::read()){

            $json = json_decode($data);
            $vip = json_decode('{ "vip": "' . $json->vip . '"}');
            $obj = new LBAPIRIP($vip, $json);
            LBAPI::print_response("Delete RIP", $obj->delete());

            
        
        } else {

            LBAPI::print_response("Delete RIP", 401);

        }

    }

    if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

        if(LBTOKEN::read()){

            $json = json_decode($data);
            $vip = json_decode('{ "vip": "' . $json->vip . '"}');
            $obj = new LBAPIRIP($vip, $json);
            LBAPI::print_response("Edit RIP", $obj->edit());
        
        } else {

            LBAPI::print_response("Edit RIP", 401);

        }


    }

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

            echo json_encode( new LBAPIRIP(null, true) );

    }

} catch (Exception $e) {

    LBAPI::logger(json_encode($e));

}

?>