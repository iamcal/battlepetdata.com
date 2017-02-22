# Installation

	cd /var/www/html
	git clone git@github.com:iamcal/battlepetdata.com.git
	cd battlepetdata.com
	ln -s /var/www/html/battlepetdata.com/site.conf /etc/apache2/sites-available/battlepetdata.com.conf
	a2ensite battlepetdata.com
	service apache2 reload
	cd db
	./init_db.sh

