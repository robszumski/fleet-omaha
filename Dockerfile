from ubuntu:12.04

RUN apt-get install -y ca-certificates openssh-client
# set up ssh
ADD id_rsa /opt/id_rsa
RUN chmod 600 /opt/id_rsa
# add tools
ADD updatectl /opt/updatectl
ADD fleetctl /opt/fleetctl
ADD update.sh /opt/update.sh
