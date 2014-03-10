<?php

global $debug;
$debug = false;

$clientInformation = array(
  "platform" => "CoreOS"
);

$applications = array(
  array(
    "name" => "test1",
    "appId" => "A5B0D8E8-D440-40AD-A76D-BB20BE23AAB3",
    "version" => "1.0.0",
    "groupId" => "84f537ab-b350-456e-abe8-5fe948fa7797",
    "imageName" => "image"
  ),
  array(
    "name" => "test2",
    "appId" => "6D01B95F-CF13-458A-8087-231833382E13",
    "version" => "1.0.0",
    "groupId" => "beta-DL360G4",
    "imageName" => "image"
  )
);

function compileXMLPayload($clientInformation, $applications) {
  $payload = '<?xml version="1.0" encoding="UTF-8"?>';
  $payload .= '<request protocol="3.0">';
  $payload .= '<os platform="' . $clientInformation['platform'] . '" version="lsb"></os>';
  foreach($applications as $app) {
    $payload .= '<app appid="' . $app['appId'] . '" version="' . $app['version'] . '" track="' . $app['groupId'] . '" bootid="' . $app['name'] . '">';
    $payload .= '<event eventtype="3" eventresult="2"></event>';
    $payload .= '</app>';
  }
  $payload .= '</request>';
  if($GLOBALS['debug']) {
    print "--- Generated payload ---\n";
    print_r($payload);
    print "\n";
  }
  return $payload;
}

function executeUpdateRequest($payload, $updateServerAddress){
  $ch = curl_init($updateServerAddress);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  if($GLOBALS['debug']) {
    print "--- Response from update server ---\n";
    print_r($response);
    print "\n";
  }
  curl_close($ch);
  //TODO: read status code

  return simplexml_load_string($response);
}

function processUpdateResponse($response, $applications) {
  $appsNeedingUpdates = [];
  foreach($response->app as $appUpdate){
    foreach($applications as $appRunning) {
      $appRunningId = (string)$appUpdate->attributes()["appid"];
      if($appRunning["appId"] == $appRunningId) {
        $newImageName = (string)$appUpdate->updatecheck->urls->url->attributes()->codebase;
        $newVersion = (string)$appUpdate->updatecheck->manifest->attributes()->version;
        $appMerged = array(
          "name" => $appRunning["name"],
          "appId" => $appRunningId,
          "oldVersion" => $appRunning["version"],
          "newVersion" => $newVersion,
          "groupId" => $appRunning["groupId"],
          "oldImageName" => $appRunning["imageName"],
          "newImageName" => $newImageName
        );
        array_push($appsNeedingUpdates, $appMerged);
      }
    }
  }
  return $appsNeedingUpdates;
}

$updatePayload = compileXMLPayload($clientInformation, $applications);
$updateResponse = executeUpdateRequest($updatePayload, "http://localhost:8000/v1/update/");
$appsNeedingUpdates = processUpdateResponse($updateResponse, $applications);

print_r($appsNeedingUpdates);

?>