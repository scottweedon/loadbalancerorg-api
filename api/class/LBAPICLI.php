<?php

class LBAPICLI {

    public function __construct() {

    }

    public static function execute($action, $object) {

        $output=null;
        $retval=null;
        $return = 500;

        $command = "/usr/local/sbin/lbcli --action " . $action;

        foreach ($object as $key => $value) {
            if (trim($value) != "") {
                if ($value == trim($value) && strpos($value, ' ') !== false) {
                    $command .= " --$key '" . trim($value) . "'";
                } else {
                    $command .= " --$key " . trim($value);
                }
            }
        }

        //TODO: Log the command here
        LBAPI::logger("CMD: " . $command);

        exec($command, $output, $retval); 

        LBAPI::logger("RSP: " . json_encode($output));

        foreach($output as $line){

            if(strpos($line, '"status":') != 0){

                $ln = json_decode($line);

                if(isset($ln->lbcli->status)){
                    switch($ln->lbcli->status){
                        case "success": $return = 200; break;
                        case "failed": $return = 400; break;
                        case "exists": $return = 409; break;
                        case "missing": $return = 404; break;
                        case "fatal": $return = 400; break;
                        default: $return = 500; break;
                    }
                } else if(isset($ln->lbcli[0]->status)) {

                    switch($ln->lbcli[0]->status){
                        case "success": $return = 200; break;
                        case "failed": $return = 400; break;
                        case "exists": $return = 409; break;
                        case "missing": $return = 404; break;
                        case "fatal": $return = 400; break;
                        default: $return = 500; break;
                    }

                } else {

                    switch($ln[0]->lbcli[0]->status) {
                        case "success": $return = 200; break;
                        case "failed": $return = 400; break;
                        case "exists": $return = 409; break;
                        case "missing": $return = 404; break;
                        case "fatal": $return = 400; break;
                        default: $return = 500; break;
                    }
                }
        
            } else {

                if(isset($object->vip)){
                    if(strpos($line, "  exists") != 0){
                        $return = 409;
                    }
                }

            }

        }     

        return $return;

    }

    public static function execute_raw($action, $params) {

        $output=null;
        $retval=null;
        $return = 0;

        $command = "/usr/local/sbin/lbcli --action " . $action . " " . $params;

        LBAPI::logger("CMD: " . $command);

        exec($command, $output, $retval); 

        LBAPI::logger("RSP: " . json_encode($output));

        foreach($output as $line){

            LBAPI::logger("Error: " . $line);

            if(strpos($line, '"status":') != 0){
                
                if(isset(json_decode($line)->lbcli->status)){
                    switch(json_decode($line)->lbcli->status){
                        case "success": $return = 200; break;
                        case "failed": $return = 500; break;
                        case "exists": $return = 409; break;
                        case "missing": $return = 404; break;
                        case "fatal": $return = 400; break;
                        default: $return = 500; break;
                    }
                } else {
                    switch(json_decode($line)->lbcli[0]->status){
                        case "success": $return = 200; break;
                        case "failed": $return = 500; break;
                        case "exists": $return = 409; break;
                        case "missing": $return = 404; break;
                        case "fatal": $return = 400; break;
                        default: $return = 500; break;
                    }
                }
        
            } else {

                if(strpos($line, '  exists') != 0){
                    return 409;
                }

            }

        }     
        
        return $return;

    }

    public static function getconfig($raw = false) {

        $output=null;
        $retval=null;
        $return = 0;

        $command = "/usr/local/sbin/lbcli --action list --function dumpconfig";

        exec($command, $output, $retval); 

        if($raw) {
        
            return $output[1];

        } else {

            $config = new LBAPIConfig();
            $config->convert($output[1]);
            return json_encode($config);


        }


    }

}



?>