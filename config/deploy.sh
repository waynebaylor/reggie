#!/bin/bash

#
# script used to deploy workspace code to local web server.
#

rm -rf /tmp/reggie
mkdir /tmp/reggie
cp -r /var/www/reggie/files/* /tmp/reggie

rm -rf /var/www/reggie/*
cp -r /home/wtaylor/workspace/reggie/src/public_html/* /var/www/reggie
cp /home/wtaylor/workspace/reggie/config/.htaccess /var/www/reggie
rm -rf `find /var/www/reggie -type d -name .svn`

touch /var/www/reggie/hhreg.error
touch /var/www/reggie/payment.log
chmod a+w /var/www/reggie/hhreg.error
chmod a+w /var/www/reggie/payment.log
cp -r /tmp/reggie/* /var/www/reggie/files
chmod -R a+rwx /var/www/reggie/files

