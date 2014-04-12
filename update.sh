#!/bin/bash

#UPDATE_SERVICE_VERSION
#UPDATE_SERVICE_APP_ID
#UPDATE_SERVICE_OLD_VERSION
#UPDATE_SERVICE_UNIT_NAME=apache-1.service

if [ -n "$UPDATE_SERVICE_OLD_VERSION" ]; then
  echo "Update available for app $UPDATE_SERVICE_APP_ID"
  echo "Updating $UPDATE_SERVICE_UNIT_NAME from  $UPDATE_SERVICE_OLD_VERSION -> $UPDATE_SERVICE_VERSION"
  # Read existing unit
  fleetctl cat $UPDATE_SERVICE_UNIT_NAME > $UPDATE_SERVICE_UNIT_NAME
  # Stop existing unit
  fleetctl destroy $UPDATE_SERVICE_UNIT_NAME
  # Replace version
  sed -i.old s/$UPDATE_SERVICE_OLD_VERSION/$UPDATE_SERVICE_VERSION/g $UPDATE_SERVICE_UNIT_NAME
  echo "Launching $UPDATE_SERVICE_UNIT_NAME:"
  cat $UPDATE_SERVICE_UNIT_NAME
  # Start new unit
  fleetctl start $UPDATE_SERVICE_UNIT_NAME
  # Clean up files
  echo "rm $UPDATE_SERVICE_UNIT_NAME"
  echo "rm $UPDATE_SERVICE_UNIT_NAME.old"
fi
