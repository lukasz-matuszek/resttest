<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . "/../vendor/autoload.php";

use Lib\RestClient;

//=======================
echo "REST TEST<br>";

$restClient = new  RestClient();
$restClient->setJwtAuthMethod();
$result = $restClient->get([ 'drilldowns'=>'Nation' , 'measures'=>'Population'],'data');

print_r($result);
exit;
//=============================================
