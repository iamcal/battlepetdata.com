#!/bin/bash

ln -s /var/www/html/battlepetdata.com/site.conf /etc/apache2/sites-available/battlepetdata.com.conf
a2ensite battlepetdata.com
service apache2 reload

cd /var/www/html/battlepetdata.com/db
./init_db.sh

