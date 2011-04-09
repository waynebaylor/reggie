#!/bin/bash

#
# usage: $> make_release.sh <release-name>
#

curr_dir=$(pwd)
 
# create the tag for the release.
#svn copy svn://dino/baylorsc/reggie/trunk svn://dino/baylorsc/reggie/tags/$1 -m "making another release"

# check out the tag.
svn checkout svn://dino/baylorsc/reggie/tags/$1 $1-tmp

# build optimized js.
mkdir $1-js
mv $1-tmp/config/dojo-release-1.5.0-src.tar.gz $1-js
tar -C $1-js -xvzf dojo-release-1.5.0-src.tar.gz
cp $1-tmp/src/config/reggie.profile.js $1-js/dojo-release-1.5.0-src/util/buildscripts/profiles
cd $1-js/dojo-release-1.5.0-src/util/buildscripts
./build.sh profile=reggie
cd $curr_dir

# remove unoptimized js.
rm -rf $1-tmp/src/public_html/js/dojo
rm -rf $1-tmp/src/public_html/js/dojox
rm -rf $1-tmp/src/public_html/js/dijit

# copy optimized js into proj.
cp -r $1-js/build-output/js/dojo $1-tmp/src/public_html/js
cp -r $1-js/build-output/js/dojox $1-tmp/src/public_html/js
cp -r $1-js/build-output/js/dijit $1-tmp/src/public_html/js

# clean up js working dir.
rm -rf $1-js

# package up all the needed files.
mv $1-tmp/src/public_html $1
mv $1-tmp/config/.htaccess $1
echo "$1" > $1/version.txt

mkdir $1-sql
mv $1-tmp/sql/*.sql $1-sql

rm -rf $1-tmp

# remove svn dirs
rm -rf `find ./$1 -type d -name .svn`

tar -cvf $1.tar $1 $1-sql

gzip $1.tar

rm -rf $1 $1-sql

# comments
echo "======================================================================="
echo "= TODO AFTER SITE UPLOAD"
echo "======================================================================="

echo ".htaccess: comment out the magic quotes line."

echo "create file: /home/web/users/10514.baylorsc/sites/baylorsc/reggie.log"

echo "Config.php: set ERROR_LOG and PAYMENT_LOG."
echo "Config.php: set DB_HOST = f1-udb02.adhost.com"
echo "Config.php: set DB_NAME = baylorsc__reggie"
echo "Config.php: set DB_USERNAME = 10514.sql"
echo "Config.php: set DB_PASSWORD = 0F96Swwr"

echo "======================================================================="

