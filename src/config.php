<?php
global $apiConfig;
$apiConfig = array(
	'oauth2_client_id' => '',
	'oauth2_secret' => '',  // Generatea key from Azure Management Portal
	'oauth2_redirect' => 'http://localhost/office365-api-php-client/examples/oauth2.php',   //example url
	'state' => '5fdfd60b-8457-4536-b20f-fcb658d19458'  //any unquiue key to Check against CSRF attack

	'resource' => 'https://outlook.office365.com/',
	'oauth2_auth_url' => 'https://login.windows.net/common/oauth2/authorize',
	'oauth2_token_url' => 'https://login.windows.net/common/oauth2/token',
);
?>