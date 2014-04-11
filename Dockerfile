from ubuntu:12.04

RUN apt-get update
RUN apt-get install -y wget ca-certificates build-essential bzr
RUN apt-get install -y git mercurial
ENV PATH $PATH:/usr/local/go/bin
ENV GOPATH /usr/local/go/
RUN wget --no-verbose https://go.googlecode.com/files/go1.2.src.tar.gz
RUN tar -v -C /usr/local -xzf go1.2.src.tar.gz
RUN cd /usr/local/go/src && ./make.bash --no-clean 2>&1
RUN git clone https://github.com/robszumski/vulcand.git
RUN cd /vulcand
RUN make deps
#RUN make install
#RUN run
#RUN vulcand -etcd=http://172.17.42.1:4001 -etcdKey=/vulcan
