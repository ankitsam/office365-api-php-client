<?php

require_once('../src/Office365_Client.php');
session_start();

$client = new Office365_Client();

$forward_url = $client->createAuthUrl();

if(isset($_GET['code'])) {

    //TODO: verfiy unquie key state to check CSRF attack
    
	$code = $_GET['code'];
	$client->setCode($code);
    $responseObj = $client->getTokens();

	// Tada: we have an access token!
	//print_r($responseObj);
    echo "Access token:<br/> " . $client->getAccessToken() . '<br/><br/>';
    echo "Refresh token:<br/> " . $client->getRefreshToken() . '<br/>';
} else{
	print "<a class='login' href='$forward_url'>Connect Me!</a>";
}

?>