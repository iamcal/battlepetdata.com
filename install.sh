#!/bin/bash

ln -s /var/www/html/battlepetdata.com/site.conf /etc/apache2/sites-available/battlepetdata.com.conf
a2ensite battlepetdata.com
service apache2 reload

cd /var/www/html/battlepetdata.com
chgrp www-data templates_c
chmod g+w templates_c

cd /var/www/html/battlepetdata.com/db
./init_db.sh

