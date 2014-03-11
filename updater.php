<?php

include "omaha.php";
include "fleet.php";

$unitName = $argv[1];
$appID = $argv[2];
$groupID = $argv[3];
$unitVersion = $argv[4];

$debug = true;

$clientInformation = array(
  "platform" => "CoreOS"
);

$application = array(
  "name" => $unitName,
  "appId" => $appID,
  "version" => $unitVersion,
  "groupId" => $groupID
);

$unitsToBeStarted = [];
$updateResponse = fetchUpdate($clientInformation, $application, false);
if($updateResponse) {
  //Generate a new name using the old prefix with a new hash
  $existingPrefix = explode(".", $unitName)[0];
  $newHash = substr(md5(time()), 0, 6);
  $newUnitName = $existingPrefix . "." . $newHash . ".service";
  $updateResponse["name"] = $newUnitName;
  //Add app unit to be started
  array_push($unitsToBeStarted, $updateResponse);
  //Add update unit to be started
  array_push($unitsToBeStarted, array(
    "location" => "https://gist.githubusercontent.com/robszumski/e1c3054144f648e6eb7f/raw/6ed19471cb82e44e3cc3c0ca8782ed8dd4a34341/gistfile1.txt",
    "name" => "updater@" . $newUnitName,
    "version" => $updateResponse["version"],
    "linkedunit" => $newUnitName
  ));
  //Start units
  startUnits($unitsToBeStarted, $application, true);
} else {
  if($debug) print "No update needed for " . $unitName . "\n";
}

?>