Papaki Module Whmcs
===========================
 
General Info
------------
Supports WHMCS V7.7.1
To module μπορεί να χρησιμοποιηθεί μόνο από whmcs V7 και πάνω.

Installation
------------

.. code-block:: bash

	Upload the folder modules/registrars/papaki in the folder modules/registrars on your server.
	
	Login in Whmcs Admin Panel and follow the steps below:

	1) Go to configuration->general settings -> localization, at system charset select utf-8.
	2) Go to configuration-> domain registrars->registrar settings select papaki and then fill
	your apikey from Papaki.gr.
	3) Go to configuration->domain pricing, add all the extensions you want. Select Papaki
	as Registrar.
	4) Go to configuration->Payment Gateways and Select Payment Ways.
	5) Make any other changes from configuration.
	6) Check EPP Code checkbox.
	
	
	For Domain Name Search:
    1) Open the file whois.json which is in the folder "resources/domains/"
    2) Open the file whois/whois.php and put your apikey.
    3) Go to the folder whmcs at your server and upload the folder whois
    4) At the file whois.json replace the word "mysite" with your site url to whois/whois.php
    5) Αν το αρχείο /resources/domains/whois.json υπάρχει ήδη θα πρέπει να προσθέσετε το περιεχόμενο αλλιώς σνεβάστε το  αρχείο whois.json στον server, στο      μονοπάτι /resources/domains/
    (See here  http://docs.whmcs.com/WHOIS_Servers )


Domain Additional Fields
-----
.. code-block:: bash

	Company title is an additional domain field for GR registry.Please upload the file
	/resources/domains/additionalfields.php . Αν το αρχείο /resources/domains/additionalfields.php υπάρχει ήδη στον server θα πρέπει να προσθέσετε στο        υπάρχον το περιεχόμενο του αρχείου.

Lang Overrides
-----
.. code-block:: bash

	Extra langstrings are included at the folder overrides/. Please upload the folder  overrides/ into
	the lang/ folder on your whmcs installation.

    Αν το αρχείο /lang/overrides/english.php  υπάρχει ήδη στον server θα πρέπει να προσθέσετε στο υπάρχον το περιεχόμενο του αρχείου
    και όχι να το αντικαταστήσετε.

    Αν το αρχείο /lang/overrides/greek.php  υπάρχει ήδη στον    server θα πρέπει να προσθέσετε στο υπάρχον το περιεχόμενο του αρχείου
    και όχι να το    αντικαταστήσετε.


HOOKS and SYNCHRONIZATION require whmcs version greater than 5.1.3 and php greater than 3 

HOOKS
-----
.. code-block:: bash

	This hook is used to synchronize the expiry date and the next renew date, after
	a successful registration or domain renewal.
	
	1) You have to enable whmcs api as you can see at:
	https://developers.whmcs.com/api/authentication/ at section "Authenticating With Login Credentials"
	2) Open the file domainregistrationhook.php and place:
	API URL (https://www.mysite.gr/whmcs/includes/api.php)
	API_USERNAME ( admin username )
	API_PASSWORD ( admin password )
	apikey from papaki
	3) Upload the file domainregistrationhook.php at the folder includes/hooks/
	4) Make a registration to check



SYNCHRONIZATION
---------------
.. code-block:: bash

	If you want to synchronize the expiration date of the domains with Papaki you can use a cron job

	1)Settings related to domain synchronizing can be found in the Setup > General Settings > Domains tab.
	There are 3 key settings:
	Domain Sync Enabled - This must be ticked in order to allow the domain sync cron to actually run.
	Sync Next Due Date - This setting should be enabled, if you want the synchronization process to
	automatically update the next due dates
	to match the dates at the expiry fields.
	Domain Sync Notify Only - This option there is in case you want WHMCS to run the sync checks and
	report any inconsistencies to you, but not actually make any updates to the domains
	automatically. With this enabled you simply get an email report, listing any discrepancies between
	data at the registrar and those held in WHMCS. 	
	
	2) According to this link
	http://docs.whmcs.com/Domains_Tab#Domain_Sync_Enabled
	enable the cron
	php -q /path/to/home/public_html/whmcspath/crons/domainsync.php

	Please to avoid blocking your profile, use this cron only once a day.


TEST ENVIRONMENT
----------------

.. code-block:: bash

	If you want to use the test environment :
	Go to configuration-> domain registrars->registrar settings select papaki , 
	fill your test apikey and the test post url: https://api-test.papaki.com/register_url2.aspx.


 

System Requirements
-------------------
*  Papaki   APIKey is required



Copyright
---------
Papaki
