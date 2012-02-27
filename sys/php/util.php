<?php
  
    /****************************************************************/
    /* Pancake                                                      */
    /* util.php                                                     */
    /* 2012 Yussuf "pp3345" Khalil                                  */
    /* License: http://creativecommons.org/licenses/by-nc-sa/3.0/   */
    /****************************************************************/
    
    //define('PHP_SAPI', 'pancake');
    
    function php_sapi_name() {
        return 'pancake';
    } 
    
    function setcookie($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false) {
        global $Pancake_request;
        return $Pancake_request->setCookie($name, $value, $expire, $path, $domain, $secure, $httponly); 
    }
    
    function header($string, $replace = true, $http_response_code = 0) {
        global $Pancake_request;
        
        $header = explode(':', $string, 2);
        $Pancake_request->setHeader($header[0], trim($header[1]));
        
        if($http_response_code)
            $Pancake_request->setAnswerCode($http_response_code);
    }
    
    function header_remove($name) {
        global $Pancake_request;
        
        $Pancake_request->removeHeader($name);
    }
    
    function headers_sent() {
        return false;
    }
?>