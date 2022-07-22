<?php

include("LBAPIRIP.php");
include("LBAPIVIP.php");
include("LBAPIACL.php");
include("LBAPIHEADER.php");
include("LBAPIWAF.php");
include("LBAPIGSLB.php");
include("LBAPIService.php");
include("LBAPIPBR.php");
include("LBAPIFIP.php");
include("LBAPICERT.php");
include("LBAPISSL.php");
include("LBAPISNI.php");
 

class LBAPIConfig {

    public $L7_vips;         // Layer 7 VIPs - Array of L7DeploymentVIP
    public $L4_vips;         // Layer 4 VIPs - Array of L4DeploymentVIP
    public $wafs;            // Array of LBAPIWAF
    public $gslb;            // Array of LBAPIGSLB
    public $service;         // Array of LBAPIService
    public $pbr;             // Array of LBAPIPBR
    public $fip;             // Array of LBAPIFIP
    public $header;          // Array of LBAPIHEADER
    public $ssl;             // Array of LBAPISSL
    public $sni;             // Array of LBAPISNI
    public $cert;            // Array of LBAPICERT

    public function __construct($data = null, $template = false){

        $this->L7_vips = array();
        $this->L4_vips = array();
        $this->acl = array();
        $this->header = array();
        $this->wafs = array();
        $this->gslb = array();
        $this->service = array();
        $this->pbr = array();
        $this->fip = array();
        $this->ssl = array();
        $this->sni = array();
        $this->cert = array();

        // Echo out an empty config
        if($template && $data == null){
            array_push($this->L7_vips, new L7LBAPIVIP());
            array_push($this->L4_vips, new L4LBAPIVIP());
        }

        if($data != null){

            foreach($data->L7_vips as $vip){
                array_push($this->L7_vips, new L7LBAPIVIP($vip, $template));
            }

            foreach($data->L4_vips as $vip){
                array_push($this->L4_vips, new L4LBAPIVIP($vip, $template));
            }

            foreach($data->acl as $acl){
                array_push($this->acl, new LBAPIACL($acl));
            }

            foreach($data->header as $header){
                array_push($this->header, new LBAPIHEADER($header));
            }

            foreach($data->wafs as $waf){
                array_push($this->wafs, new LBAPIWAF($waf));
            }

            foreach($data->gslb as $gslb){
                array_push($this->gslb, new LBAPIGSLB($gslb));
            }

            foreach($data->service as $service){
                array_push($this->service, new LBAPIService($service));
            }

            foreach($data->pbr as $pbr){
                array_push($this->pbr, new LBAPIPBR($pbr));
            }

            foreach($data->fip as $fip){
                array_push($this->pbr, new LBAPIFIP($fip));
            }

            foreach($data->cert as $cert){
                array_push($this->cert, new LBAPICERT($cert));
            }

            foreach($data->ssl as $ssl){
                array_push($this->ssl, new LBAPISSL($ssl));
            }

            foreach($data->sni as $sni){
                array_push($this->sni, new LBAPISNI($sni));
            }

        }

    }

    public function apply() {

        try {

            foreach($this->L7_vips as $vip){
                
                if($vip->add() == 200){
                
                    foreach($vip->rips as $rip){
                        $rip->add();
                    }

                }
                
            }

            foreach($this->L4_vips as $vip){
                
                if($vip->add() == 200){
                
                    foreach($vip->rips as $rip){
                        $rip->add();
                    }

                }
                
            }

            foreach($this->acl as $acl){ $acl->add(); }
            foreach($this->wafs as $waf){ $waf->add(); }
            foreach($this->gslb as $gslb){ $gslb->add(); }
            foreach($this->pbr as $pbr){ $pbr->add(); }
            foreach($this->fip as $fip){ $fip->add(); }
            foreach($this->cert as $cert){ $cert->add(); }
            foreach($this->ssl as $ssl){ $ssl->add(); }
            foreach($this->sni as $sni){ $sni->add(); }

            foreach($this->service as $service){ $service->execute(); }

        } catch (Exception $e) {

            var_dump($e);
            throw $e;
            
        }

    }

    public function convert($config) {

        // convert the config into API v3 format
        $config = json_decode(str_replace("[]", "null", $config));

        // create layer 4 vips
        foreach($config->ldirectord->virtual as $data){
            
            $new_vip = new L4LBAPIVIP();

            $new_vip->vip = $data->label;
            $new_vip->ip = $data->server;
            $new_vip->ports = $data->ports;
            $new_vip->layer = 4;
            $new_vip->protocol = $data->protocol;                    
            $new_vip->forwarding = $data->forwardingmethod;                      
            $new_vip->granularity = $data->granularity;                     
            $new_vip->fallback_ip = $data->fallback->ip;                     
            $new_vip->fallback_port = $data->fallback->port;                      
            $new_vip->fallback_local = $data->masq;                  
            $new_vip->persistent = $data->persistent;                     
            $new_vip->persist_time = $data->persist_time;                    
            $new_vip->scheduler = $data->scheduler;                       
            $new_vip->feedback = $data->feedbackmethod;                        
            $new_vip->email = $data->emailalert->to;                          
            $new_vip->email_from = $data->emailalert->from;                     
            $new_vip->check_service = $data->service->check;                  
            $new_vip->check_vhost = $data->service->vhost;                    
            $new_vip->check_database = $data->service->database;                  
            $new_vip->check_login = $data->service->login;                     
            $new_vip->check_password = $data->service->password;                  
            $new_vip->check_type = $data->service->check->type;                      
            $new_vip->check_port = $data->service->check->port;                      
            $new_vip->check_request = $data->service->check->request;                   
            $new_vip->check_response = $data->service->check->response;                  
            $new_vip->check_secret = $data->service->secret;                    
            $new_vip->check_command = $data->service->check->command;                   

            $new_vip->rips = array();

            if(isset($data->real)){
                
                if(is_array($data->real)){

                    foreach($data->real as $real){

                        $new_rip = new LBAPIRIP();

                        $new_rip->vip = $new_vip->vip;
                        $new_rip->rip = $real->label;
                        $new_rip->ip = $real->server;
                        $new_rip->weight = $real->weight;
                        $new_rip->port = $real->port;
                        $new_rip->minconns = $real->minconns;
                        $new_rip->maxconns = $real->maxconns;
                        $new_rip->encrypted = $real->encrypted;
                        
                        array_push($new_vip->rips, $new_rip);

                    }


                } else {

                    $new_rip = new LBAPIRIP();

                    $new_rip->vip = $new_vip->vip;
                    $new_rip->rip = $data->real->label;
                    $new_rip->ip = $data->real->server;
                    $new_rip->weight = $data->real->weight;
                    $new_rip->port = $data->real->port;
                    $new_rip->minconns = $data->real->minconns;
                    $new_rip->maxconns = $data->real->maxconns;
                    $new_rip->encrypted = $data->real->encrypted;
                    
                    array_push($new_vip->rips, $new_rip);

                }

            }

            array_push($this->L4_vips, $new_vip);

        }

        // create layer 7 vips
        foreach($config->haproxy->virtual as $data){
            
            $new_vip = new L7LBAPIVIP();

            $new_vip->vip = $data->label;
            $new_vip->ip = $data->server;
            $new_vip->ports = $data->ports;
            $new_vip->layer = 7; 
            $new_vip->mode = $data->mode;              
            //$new_vip->persistence = $data->persistence;                    
            $new_vip->cookiename = $data->cookie;                     
            $new_vip->fallback_ip = $data->fallback->ip;                  
            $new_vip->fallback_port = $data->fallback->port;                 
            $new_vip->persist_time = $data->persist_time;                   
            $new_vip->persist_table_size = $data->persist_table_size;            
            $new_vip->maxconn = $data->maxconn;                        
            $new_vip->scheduler = $data->scheduler;                      
            $new_vip->check_port = $data->check->port;                     
            $new_vip->check_request = $data->check->request;                  
            $new_vip->check_receive = $data->check->response;                  
            $new_vip->check_host = $data->check->host;                    
            $new_vip->check_username = $data->check->username;                 
            $new_vip->appsession_cookie = $data->appsession->cookie;            
            $new_vip->forward_for = $data->forward_for;                    
            $new_vip->http_pipeline = $data->http_pipeline;                  
            $new_vip->http_pretend_keepalive = $data->http_pretend_keepalive;         
            $new_vip->stunneltproxy = $data->stunneltproxy;                  
            $new_vip->feedback_method = $data->feedback;              
            $new_vip->fallback_persist = $data->fallback->persist;               
            $new_vip->feedback_port = $data->feedback_port;                 
            $new_vip->check_type = $data->check->type;                     
            //$new_vip->external_check_script = $data->external_check_script;          
            $new_vip->tcp_keep_alive = $data->tcp_keep_alive;                 
            $new_vip->force_to_https = $data->force_https;                 
            //$new_vip->timeout = $data->timeout;                        
            $new_vip->timeout_client = $data->timeout_client;                 
            $new_vip->timeout_server = $data->timeout_server;                 
            $new_vip->redirect_code = $data->https_redirect_code;                  
            //$new_vip->no_write = $data->no_write;                       
            $new_vip->waf_label = $data->waf_service_label;                      
            $new_vip->clear_stick_drain = $data->clear_stick_drain;              
            $new_vip->compression = $data->compression;                   
            //$new_vip->autoscale_group = $data->autoscale_group;               
            $new_vip->cookie_maxidle = $data->cookie_maxidle;
            $new_vip->cookie_maxlife = $data->cookie_maxlife;
            $new_vip->source_address = $data->source_address;
            $new_vip->backend_encryption = $data->backend_encryption;
            $new_vip->enable_hsts = $data->enable_hsts;
            $new_vip->hsts_month = $data->hsts_month;
            $new_vip->xff_ip_pos = $data->xff_ip_pos;                    
            $new_vip->invalid_http = $data->accept_invalid_request;                   
            $new_vip->send_proxy = $data->send_proxy;                     
            //$new_vip->as_port = $data->as_port;                        
            $new_vip->http_request = $data->http_request;                  
            $new_vip->stunnel_source = $data->stunneltproxy;                 
            //$new_vip->proxy_bind = $data->proxy_bind;                     
            //$new_vip->slave_ip = $data->slave_ip;                       
            $new_vip->tunneltimeout = $data->tunneltimeout;                
            $new_vip->redispatch = $data->redispatch;                   
            $new_vip->fallback_encrypt = $data->fallback->encrypt;               
            //$new_vip->http_reuse_connection = $data->http_reuse_connection;         
            $new_vip->tproxy = $data->transparentproxy;
            
            $new_vip->rips = array();

            if(isset($data->real)){
                
                if(is_array($data->real)){

                    foreach($data->real as $real){

                        $new_rip = new LBAPIRIP();

                        $new_rip->vip = $new_vip->vip;
                        $new_rip->rip = $real->label;
                        $new_rip->ip = $real->server;
                        $new_rip->weight = $real->weight;
                        $new_rip->port = $real->port;
                        $new_rip->minconns = $real->minconns;
                        $new_rip->maxconns = $real->maxconns;
                        $new_rip->encrypted = $real->encrypted;
                        
                        array_push($new_vip->rips, $new_rip);

                    }

                } else {

                    $new_rip = new LBAPIRIP();

                    $new_rip->vip = $new_vip->vip;
                    $new_rip->rip = $data->real->label;
                    $new_rip->ip = $data->real->server;
                    $new_rip->weight = $data->real->weight;
                    $new_rip->port = $data->real->port;
                    $new_rip->minconns = $data->real->minconns;
                    $new_rip->maxconns = $data->real->maxconns;
                    $new_rip->encrypted = $data->real->encrypted;
                    
                    array_push($new_vip->rips, $new_rip);

                }

            }

            array_push($this->L7_vips, $new_vip);

        }
        
    }

}

?>