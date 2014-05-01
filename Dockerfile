from ubuntu:12.04

RUN apt-get install -y ca-certificates
ADD updatectl /opt/updatectl
ADD update.sh /opt/update.sh
