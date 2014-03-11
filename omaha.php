<?php

function compileXMLPayload($clientInformation, $application, $debug) {
  $payload = '<?xml version="1.0" encoding="UTF-8"?>';
  $payload .= '<request protocol="3.0">';
  $payload .= '<os platform="' . $clientInformation['platform'] . '" version="lsb"></os>';
  $payload .= '<app appid="' . $application['appId'] . '" version="' . $application['version'] . '" track="' . $application['groupId'] . '" bootid="' . $application['name'] . '">';
  $payload .= '<event eventtype="3" eventresult="2"></event>';
  $payload .= '</app>';
  $payload .= '</request>';
  if($debug) {
    print "Generated payload sent to update service:\n";
    print_r($payload);
    print "\n";
  }
  return $payload;
}

function executeUpdateRequest($payload, $updateServerAddress, $debug){
  $ch = curl_init($updateServerAddress);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  if($debug) {
    print "Response from update server:\n";
    print_r($response);
    print "\n";
  }
  curl_close($ch);
  //TODO: read status code

  return simplexml_load_string($response);
}

function processUpdateResponse($response, $application, $debug) {
  $appsNeedingUpdates = [];
  foreach($response->app as $appUpdate){
    $updateStatus = (string)$appUpdate->updatecheck->attributes()->status;
    if($debug) "Update service returned " . $updateStatus;

    if($updateStatus != "noupdate") {
      $appRunningId = (string)$appUpdate->attributes()["appid"];
      $updateLocation = (string)$appUpdate->updatecheck->urls->url->attributes()->codebase;
      $updateLocation = str_replace("http://update-storage.core-os.net/production/", "", $updateLocation);
      $newVersion = (string)$appUpdate->updatecheck->manifest->attributes()->version;

      if($debug) print "Update image: " . $updateLocation . "\n";
      if($debug) print "Update version: " . $newVersion . "\n";

      $updateLocation = "https://gist.githubusercontent.com/robszumski/a4c45e7da35a6f81c984/raw/83fcaf12f250c8c51b5805bda9c707164838aa75/gistfile1.txt";

      return array(
        "location" => $updateLocation,
        "version" => $newVersion
      );
    }
  }
  return false;
}

function fetchRemoteUnit($location, $debug) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch, CURLOPT_URL, $location);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $response = curl_exec($ch);
  if($debug) {
    print "Response from remote unit location:\n";
    print_r($response);
    print "\n";
  }
  curl_close($ch);
  //TODO: read headers for status
  return $response;
}

function fetchUpdate($clientInformation, $application, $debug) {
  $updatePayload = compileXMLPayload($clientInformation, $application, $debug);
  $updateResponse = executeUpdateRequest($updatePayload, "http://localhost:8000/v1/update/", $debug);
  return processUpdateResponse($updateResponse, $application, $debug);
}

?>