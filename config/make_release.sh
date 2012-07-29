#!/bin/bash

#
# usage: $> make_release.sh <release-name>
#

dojo="dojo-release-1.6.1-src"
curr_dir=$(pwd)

svntag=$1
 
# create the tag for the release.
if [ -n "$svntag" -a "$svntag" != "trunk" ]
then
    svn copy svn://dino/baylorsc/reggie/trunk svn://dino/baylorsc/reggie/tags/$svntag -m "making another release"
else
    svntag="trunk"
fi

# check out the tag.
if [ "$svntag" != "trunk" ]
then
    svn checkout svn://dino/baylorsc/reggie/tags/$svntag $svntag-tmp
else
    svn checkout svn://dino/baylorsc/reggie/trunk $svntag-tmp
fi

# build optimized js.
mkdir $svntag-js
tar -C $svntag-js -xvzf $svntag-tmp/config/$dojo.tar.gz
cp $svntag-tmp/config/reggie.profile.js $svntag-js/$dojo/util/buildscripts/profiles
cp -r $svntag-tmp/src/public_html/js/hhreg $svntag-js/$dojo
cd $svntag-js/$dojo/util/buildscripts
./build.sh profile=reggie
cd $curr_dir

# remove unoptimized js.
rm -rf $svntag-tmp/src/public_html/js/dojo
rm -rf $svntag-tmp/src/public_html/js/dojox
rm -rf $svntag-tmp/src/public_html/js/dijit

# copy optimized js into proj.
cp -r $svntag-js/build-output/js/dojo $svntag-tmp/src/public_html/js
cp -r $svntag-js/build-output/js/dojox $svntag-tmp/src/public_html/js
cp -r $svntag-js/build-output/js/dijit $svntag-tmp/src/public_html/js

# clean up js working dir.
rm -rf $svntag-js

# package up all the needed files.
mv $svntag-tmp/src/public_html $svntag
mv $svntag-tmp/config/.htaccess $svntag
echo "$svntag" > $svntag/version.txt

mkdir $svntag-sql
mv $svntag-tmp/sql/*.sql $svntag-sql

rm -rf $svntag-tmp

# remove svn dirs
rm -rf `find ./$svntag -type d -name .svn`

tar -cvf $svntag.tar $svntag $svntag-sql

gzip $svntag.tar

rm -rf $svntag $svntag-sql

# comments
echo "======================================================================="
echo "= TODO AFTER SITE UPLOAD"
echo "======================================================================="
echo "1. Create log files (error and payment)"
echo "2. Set database properties"
echo "3. Configure SSL in .htaccess"
echo "======================================================================="

