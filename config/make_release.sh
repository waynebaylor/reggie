#!/bin/bash

#
# usage: $> make_release.sh <release-name>
#

# create the tag for the release.
#svn copy svn://dino/baylorsc/reggie/trunk svn://dino/baylorsc/reggie/tags/$1 -m "making another release"

# check out the new tag.
svn checkout svn://dino/baylorsc/reggie/tags/$1 $1-tmp

mv $1-tmp/src/public_html $1
mv $1-tmp/config/.htaccess $1

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

