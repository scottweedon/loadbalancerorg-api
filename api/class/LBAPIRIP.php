<?php

class LBAPIRIP {

    public $vip;                             //<VIP Name>
    public $rip;                             //<RIP Name>
    public $ip;                              //<RIP IP Address>
    public $weight;                          //<Weight value>
    public $port;                            //<Port value>
    public $minconns;                        //<minconns>
    public $maxconns;                        //<maxconns>
    public $encrypted;                       //<on|off>

    function __construct($vip, $rip = null){

        // set defaults
        $this->vip = $vip->vip;
        $this->port = null;
        $this->weight = 100;
        $this->minconns = 0;
        $this->maxconns = 0;
        $this->encrypted = null;

        if($vip->layer == 7){ $this->encrypted = "off"; } 

        if($rip != null){

            if(isset($rip->vip) && $rip->vip != null){ $this->vip = $rip->vip; }
            if(isset($rip->rip) && $rip->rip != null){ $this->rip = $rip->rip; }
            if(isset($rip->ip) && $rip->ip != null){ $this->ip = $rip->ip; }
            if(isset($rip->port) && $rip->port != null){ $this->port = $rip->port; }
            if(isset($rip->weight) && $rip->weight != null){ $this->weight = $rip->weight; }
            if(isset($rip->minconns) && $rip->minconns != null){ $this->minconns = $rip->minconns; }
            if(isset($rip->maxconns) && $rip->maxconns != null){ $this->maxconns = $rip->maxconns; }
            if(isset($rip->encrypted) && $rip->encrypted != null){ $this->encrypted = $rip->encrypted; }        

        } 

    }

    public function add($overwrite = false) {


        try {

            if( $this->vip != null ){

                if(!$this->exists()){

                    $result = LBAPICLI::execute("add-rip", $this);

                    if($this->exists()){

                        return 200;

                    } else {

                        if($result == 304){
                            
                            if($overwrite) {
                                return LBAPICLI::execute("edit-rip", $this);
                            } else {
                                return $result;
                            }

                        } else {
                            return $result;
                        } 
                        
                    }

                } else {

                    return 409;

                }

            } else {

                return 400;

            }

        } catch (Exception $e) {

            return 500;

        }


    }

    public function edit() {

        try {
            if( $this->vip != null ){

                if($this->exists()){

                    $result = LBAPICLI::execute("edit-rip", $this);
                    return $result;

                } else {

                    return 404;

                }
                
            } else {

                return 400;

            }
        } catch (Exception $e) {

            return 500;

        }

    }

    public function delete() {

        try {
            if( $this->vip != null ){

                $result = LBAPICLI::execute("delete-rip", $this);
                return $result;
                
            } else {

                return 400;

            }
        } catch (Exception $e) {

            return 500;

        }

    }

    public function exists(){

        //get config
        $config = json_decode(LBAPICLI::getconfig());

        foreach($config->L4_vips as $vip){
            if($vip->vip == $this->vip){
                foreach($vip->rips as $rip){
                    if($this->rip == $rip->rip || $this->ip == $rip->ip){
                        return true;
                    }
                }
            }
        }

        foreach($config->L7_vips as $vip){
            if($vip->vip == $this->vip){
                foreach($vip->rips as $rip){
                    if($this->rip == $rip->rip || $this->ip == $rip->ip){
                        return true;
                    }
                }
            }
        }
        
        return false;

    }

}

?>