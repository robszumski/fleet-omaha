<?php

function isUnitRunning($unitName, $debug) {
  $fleetOutput = shell_exec('fleetctl --tunnel ec2-184-73-113-121.compute-1.amazonaws.com list-units | grep ' . $unitName);
  if($debug) print "Querying fleet for status of " . $unitName . "\n";
  if($debug) print $fleetOutput;
  foreach(preg_split("/((\r?\n)|(\r\n?))/", $fleetOutput) as $matchedUnit){
    $unitExists = false;
    $unitActive = false;
    $matchedColumns = explode("\t", $matchedUnit);
    $matchedName = trim($matchedColumns[0]);
    $matchedStatus = trim($matchedColumns[3]);

    if($unitName == $matchedName) $unitExists = true;
    if($matchedStatus == "active") $unitActive = true;
    
    if($unitExists && $unitActive) {
      if($debug) print "Unit " . $unitName . " was found running.\n";
      return true;
    }
  }
  if($debug && !$unitExists) print "Unit " . $unitName . " does not exist.\n";
  if($debug && !$unitActive) print "Unit " . $unitName . " was not active.\n";
  return false;
}

function startUnits($units, $application, $debug) {
  $fleetCommand = "fleetctl --tunnel ec2-184-73-113-121.compute-1.amazonaws.com start";
  foreach($units as $unit) {
    $unitLocation = $unit["location"];
    $unitName = $unit["name"];
    $unitVersion = $unit["version"];
    $unitLink = $unit["linkedunit"];
    //load remote unit
    $unitTemplate = fetchRemoteUnit($unitLocation, false);
    //replace values
    $unitTemplate = str_replace("{{app.appid}}", $application["appId"], $unitTemplate);
    $unitTemplate = str_replace("{{app.name}}", $application["name"], $unitTemplate);
    $unitTemplate = str_replace("{{app.groupid}}", $application["groupId"], $unitTemplate);
    $unitTemplate = str_replace("{{app.version}}", $unitVersion, $unitTemplate);
    $unitTemplate = str_replace("{{app.unit}}", $unitLink, $unitTemplate);
    if($debug) print "Generated unit for " . $unit["name"] . "\n";
    if($debug) print $unitTemplate . "\n";
    //write to disk
    $pathOnDisk = __DIR__ . "/" . $unitName;
    //file_put_contents($pathOnDisk, $unitTemplate);
    $fleetCommand .= " " . $unitName;
  }
  //start all units
  if($debug) print "Starting units with " . $fleetCommand . "\n";
  //if($debug) print shell_exec($fleetCommand);
}

?>