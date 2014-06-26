<?php
// Check for the json extension, the Office 365 APIs PHP Client won't function
// without it.
if (! function_exists ( 'json_decode' )) {
    throw new Exception ( 'Office 365 PHP API Client requires the JSON PHP extension' );
}

if (! function_exists ( 'http_build_query' )) {
    throw new Exception ( 'Office 365 PHP API Client requires http_build_query()' );
}

if (! ini_get ( 'date.timezone' ) && function_exists ( 'date_default_timezone_set' )) {
    date_default_timezone_set ( 'UTC' );
}
set_include_path ( dirname ( __FILE__ ) . PATH_SEPARATOR . get_include_path () );

require_once "config.php";
// If a local configuration file is found, merge it's values with the default
// configuration
if (file_exists ( dirname ( __FILE__ ) . '/local_config.php' )) {
    $defaultConfig = $apiConfig;
    require_once (dirname ( __FILE__ ) . '/local_config.php');
    $apiConfig = array_merge ( $defaultConfig, $apiConfig );
}

class Office365_Client {
    
    private $code;
    private $accessToken;
    private $refreshToken;
    
    public function __construct($config = array()) {
        global $apiConfig;
        $apiConfig = array_merge ( $apiConfig, $config );
    }
    
    public function getAccessToken() {
        $token = $this->accessToken;
        return (null == $token || 'null' == $token) ? null : $token;
    }
    
    public function getRefreshToken() {
        $token = $this->refreshToken;
        return (null == $token || 'null' == $token) ? null : $token;
    }
    
    public function setCode($code) {
        if ($code == null || 'null' == $code) {
            $code = null;
        }
        $this->code = $code ;
    }
    
    public function createAuthUrl() {
        global $apiConfig;
        $query_params = array ('response_type' => 'code','client_id' => $apiConfig ['oauth2_client_id'],'client_secret' => $apiConfig ['oauth2_secret'],'redirect_uri' => $apiConfig ['oauth2_redirect'],'resource' => $apiConfig ['resource'],'state' => $apiConfig ['state'] 
        );
        
        $auth_url = $apiConfig ['oauth2_auth_url'] . '?' . http_build_query ( $query_params );
        return $auth_url;
    }
    
    public function getTokens() {
        global $apiConfig;
        $url = $apiConfig['oauth2_token_url'];
        $params = array ("code" => $this->code,"client_id" => $apiConfig ['oauth2_client_id'],"client_secret" =>$apiConfig ['oauth2_secret'],"resource" => $apiConfig ['resource'],"redirect_uri" => $apiConfig ['oauth2_redirect'],"grant_type" => "authorization_code" 
        );
        
        // build a new HTTP POST request
        $request = new HttpPost ( $url );
        $request->setPostData ( $params );
        $request->send();
        $responseObj = json_decode($request->getHttpResponse ());
        $this->accessToken = $responseObj->access_token;
        $this->refreshToken = $responseObj->refresh_token;
        return $responseObj;
    }
}

class HttpPost {
    public $url;
    public $postString;
    public $httpResponse;
    
    public $ch;
    
    public function __construct($url) {
        $this->url = $url;
        $this->ch = curl_init ( $this->url );
        curl_setopt ( $this->ch, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt ( $this->ch, CURLOPT_HEADER, false );
        curl_setopt ( $this->ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $this->ch, CURLOPT_SSL_VERIFYPEER, false );
    }
    
    public function __destruct() {
        curl_close ( $this->ch );
    }
    public function setPostData($params) {
        // http_build_query encodes URLs, which breaks POST data
        $this->postString = rawurldecode ( http_build_query ( $params ) );
        curl_setopt ( $this->ch, CURLOPT_POST, true );
        curl_setopt ( $this->ch, CURLOPT_POSTFIELDS, $this->postString );
    }
    
    public function send() {
        $this->httpResponse = curl_exec ( $this->ch );
    }
    
    public function getHttpResponse() {
        return $this->httpResponse;
    }
}
?>
