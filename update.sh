#!/bin/bash

#UPDATE_SERVICE_VERSION
#UPDATE_SERVICE_APP_ID
#UPDATE_SERVICE_OLD_VERSION
#FLEET_TUNNEL_IP
UPDATE_SERVICE_UNIT_NAME=$1

#start ssh-agent
#start ssh-agent
eval `ssh-agent`
ssh-add /opt/id_rsa
mkdir -p ~/.ssh
touch ~/.ssh/known_hosts
ssh-keyscan -H $FLEET_TUNNEL_IP >> ~/.ssh/known_hosts

if [ -n "$UPDATE_SERVICE_OLD_VERSION" ]; then
  echo "Update available for app $UPDATE_SERVICE_APP_ID"
  echo "Updating $UPDATE_SERVICE_UNIT_NAME from  $UPDATE_SERVICE_OLD_VERSION -> $UPDATE_SERVICE_VERSION"
  # Read existing unit
  /opt/fleetctl --strict-host-key-checking=false --tunnel $FLEET_TUNNEL_IP cat $UPDATE_SERVICE_UNIT_NAME &> $UPDATE_SERVICE_UNIT_NAME
  # Stop existing unit
  /opt/fleetctl --strict-host-key-checking=false --tunnel $FLEET_TUNNEL_IP destroy $UPDATE_SERVICE_UNIT_NAME
  # Replace version
  sed -i .bak -e "s/${UPDATE_SERVICE_OLD_VERSION}/${UPDATE_SERVICE_VERSION}/g" "$UPDATE_SERVICE_UNIT_NAME"
  echo "Launching $UPDATE_SERVICE_UNIT_NAME:"
  cat $UPDATE_SERVICE_UNIT_NAME
  # Start new unit
  /opt/fleetctl --strict-host-key-checking=false --tunnel $FLEET_TUNNEL_IP start $UPDATE_SERVICE_UNIT_NAME
fi
