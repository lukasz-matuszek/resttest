<?php

include __DIR__ . "/../vendor/autoload.php";

$apiUrl = 'XXXX';

$restClient = new RestClient($apiUrl);

//=======================
echo  "REST TEST";


$data = [
	'name'	=> 'onion',
	'id' 	=> 12343
];

$result = $restClient->callAPI('POST',$data);

//=======================

$data = [
	'variable'	=> 'name',
	'id' 	=> 12343
];

$result = $restClient->callAPI('GET',$data);