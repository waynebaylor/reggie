#!/bin/bash

#
# script used to make custom javascript build. this combines dojo with project code into
# two js files: admin and reg.
#
# 1) download the dojo SDK
# 2) copy reggie.profile.js into the util/buildscripts/profiles dir
# 3) from the util/buildscripts dir run 'build.sh profile=reggie'
# 4) build output is in ../../../buld-output dir
#

cd ~/development/dojo-sdk
rm -rf dojo-release-1.5.0-src
tar -xvzf dojo-release-1.5.0-src.tar.gz
cp -r ~/workspace/reggie/src/public_html/js/hhreg ~/development/dojo-sdk/dojo-release-1.5.0-src 
cp ~/workspace/reggie/config/reggie.profile.js ~/development/dojo-sdk/dojo-release-1.5.0-src/util/buildscripts/profiles
cd ~/development/dojo-sdk/dojo-release-1.5.0-src/util/buildscripts
./build.sh profile=reggie
cd ~/development/dojo-sdk
rm -rf dojo-release-1.5.0-src

