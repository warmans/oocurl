<?php
require_once(__DIR__.'/../vendor/autoload.php');

use OOCURL\Multi;
use OOCURL\SessionHandle;

$multi = new Multi();
$multi->addHandle(SessionHandle::create('http://google.com', array(CURLOPT_HEADER => 0)));
$multi->addHandle(SessionHandle::create('http://google.com', array(CURLOPT_HEADER => 0)));

$results = $multi->fetchAllResults(0.5);
foreach($results as $result) {
    var_dump($result->getBody());
}