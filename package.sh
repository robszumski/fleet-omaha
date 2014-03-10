#!/bin/bash

APP_ID=$1
VERSION=$2

INDEX_ADDR='index.robszumski.com:5000'
USERNAME='admin'
HAWK_TOKEN='e1a41faa6719eeab2e29029c5c74ae62'

# Grab the image's tag
# 6e88430f62c0dd6afae1a08d8b540fd2b4d097f7b503125b2a5574d45ed6bfe1
# docker ps --no-trunc | grep 6e88430f62c0dd6afae1a08d8b540fd2b4d097f7b503125b2a5574d45ed6bfe1 | cut -d' ' -f 4 | cut -d':' -f 3
# VERSION='abc123'

# Calculate sha1sum of container on disk
# sudo sha1sum /var/lib/docker/containers/6e88430f62c0dd6afae1a08d8b540fd2b4d097f7b503125b2a5574d45ed6bfe1
SHA1='91275bdf0a673c18cbe3e2e64dcbebd0d11349fa'

# Calculate sha256sum of container on disk
# sudo sha256sum /var/lib/docker/containers/6e88430f62c0dd6afae1a08d8b540fd2b4d097f7b503125b2a5574d45ed6bfe1
SHA256='ff37b56b61b23f67aa60b9544af42e40cdfe7af95ca8512687a8b53f81d3a600'

# Calculate size of the container on disk
# sudo du -c /var/lib/docker/containers/6e88430f62c0dd6afae1a08d8b540fd2b4d097f7b503125b2a5574d45ed6bfe1/ | grep total
SIZE=323668

echo "./rollerctl -u ${USERNAME} -k ${HAWK_TOKEN} new-package ${APP_ID} ${VERSION} --name ${VERSION} --path ${INDEX_ADDR} --size ${SIZE} --sha1sum ${SHA1} --sha256sum ${SHA256} --signature 'ss' --metadata-size ${SIZE}"