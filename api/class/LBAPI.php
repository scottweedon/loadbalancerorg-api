<?php

include("LBAPICLI.php");
include("LBAPIConfig.php");
include("LBAPIParameter.php");

class LBAPI {

    public $method;
    public $parameters;
    public $config;          

    public function __construct($data = null, $template = false){

        $this->method = "manual";

        if($template){
        
            $this->parameters = array(new LBAPIParameter(null, $template));
            $this->config = new LBAPIConfig(null, $template);
        
        } else {

            if($data != null){
                
                $this->method = $data->method;
                
                $this->parameters = array();
                foreach($data->parameters as $parameter){ 
                    array_push($this->parameters, new LBAPIParameter($parameter)); 
                }

                $this->config = new LBAPIConfig($data->config, false);

            } else {

                $this->parameters = array();
                $this->config = new LBAPIConfig(null);
            }

        }

        

    }

    public static function logger($logline) {

        $logfile = "/etc/loadbalancer.org/deploy.log";
        $log = fopen($logfile, "a+") or die("Unable to open file!");
        fwrite($log, date("Y/m/d h:i:s") . " - " . $logline . "\r\n");

    }

    public static function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        $headers = str_replace("Bearer ", "", $headers); 
        return $headers;
    }

    public static function print_response($action, $value){

        switch($value){
            case 200: http_response_code(200); echo '{"code": 200, "action": "' . $action . '", "message": "OK"}' ; break;
            case 400: http_response_code(400); echo '{"code": 400, "action": "' . $action . '", "message": "Bad Request"}' ; break;
            case 401: http_response_code(401); echo '{"code": 401, "action": "' . $action . '", "message": "Unauthorized"}' ; break;
            case 404: http_response_code(404); echo '{"code": 404, "action": "' . $action . '", "message": "Not Found"}' ; break;
            case 409: http_response_code(409); echo '{"code": 409, "action": "' . $action . '", "message": "Conflict"}' ; break;
            case 500: http_response_code(500); echo '{"code": 500, "action": "' . $action . '", "message": "Internal Server Error"}' ; break;
        }

    }

    public function apply(){

        $this->config->apply();
        
    }

}


if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }
}

?>