FROM ubuntu
ADD . /
RUN apt-get install git php5-cli php5 php5-curl curl