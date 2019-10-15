Papaki Module Whmcs
===========================

Γενικές πληροφορίες
------------
Υποστηρίζει WHMCS V7.7.1
 

Εγκατάσταση
------------

.. code-block:: bash

	Ανεβάστε τον φάκελο papaki μέσα στον φάκελο modules/registrars

	Στη συνέχεια μπείτε στο admin του whmcs και:

	1) Από το configuration->general settings -> localization στο system charset πεδίο valte utf-8.
	2) Από το configuration-> domain registrars->registrar settings επιλέξτε το papaki και 
	στη συνέχεια συμπληρώστε τo apiKey που έχετε στο Papaki.gr.
	3) Από το configuration->domain pricing προσθέστε όλα τα extensions που θέλετε να έχετε, 
	καθώς και τις τιμές τις οποίες θα τα χρεώνετε. Στο Automatic Registration βάλτε Papaki 
	για τα extensions τα οποία θέλετε να κατοχυρώνονται από το Papaki.gr.
	4) Από το configuration->Payment Gateways δώστε τους τρόπους που θέλετε να γίνονται 
	οι πληρωμές από το site σας.
	5) Κάντε ότι επιπλέον αλλαγές θέλετε για το site σας μέσα από το configuration.
	6) Για να μπορείτε να έχετε επιτυχή μεταφορά ονομάτων χώρου σε εσάς θα πρέπει στο 
	configuration->domain pricing να έχετε κάνει check για όλα τα extensions το κουτάκι EPP Code.
	
	
	Για να δουλεύει σωστά η αναζήτηση ονομάτων χώρου θα πρέπει να κάνετε τις εξής αλλαγές:
	A) Αν η έκδοση της whmcs σας είναι μικρότερη από 7.0.0 τότε ακολουθήσε τα παρακάτω βήματα
		1) Κάντε download από τον server σας το αρχείο includes/whoisservers.php
		2) Στο τέλος αυτού του αρχείου θα προσθέσετε τις γραμμές
		.gr|www.mysite.gr/whmcs/whois/whois.php?domainName=|HTTPREQUEST-not registered
		.com.gr|www.mysite.gr/whmcs/whois/whois.php?domainName=|HTTPREQUEST-not registered
		.net.gr|www.mysite.gr/whmcs/whois/whois.php?domainName=|HTTPREQUEST-not registered
		.gov.gr|www.mysite.gr/whmcs/whois/whois.php?domainName=|HTTPREQUEST-not registered
		.edu.gr|www.mysite.gr/whmcs/whois/whois.php?domainName=|HTTPREQUEST-not registered
		.org.gr|www.mysite.gr/whmcs/whois/whois.php?domainName=|HTTPREQUEST-not registered
		.eu|www.mysite.gr/whmcs/whois/whois.php?domainName=|HTTPREQUEST-not registered
		3) Ανοίξτε το αρχείο whois/whois.php και βάλτε το apikey που έχετε στο papaki.
		4) Κάντε upload μέσα στον φάκελο που έχετε το whmcs τον φάκελο whois.
		5) Όπου στο αρχείο whoisserver.php λέει mysite βάλτε το url για το δικό σας whois/whois.php
		το οποίο κάνατε upload.
	
	B) Αν η έκδοση της whmcs σας είναι μεγαλύτερη ή ίση της 7.0.0 τότε ακολουθήσε τα παρακάτω βήματα 
		1) Ανοίξτε το αρχείο whois.json που βρίσκεται στον φάκελο resources/domains/ στα αρχεία
		που έχετε κατεβάσει
		2) Ανοίξτε το αρχείο whois/whois.php και βάλτε το apikey που έχετε στο papaki.
		3) Κάντε upload μέσα στον φάκελο που έχετε το whmcs τον φάκελο whois.
		4) Όπου στο αρχείο whois.json  λέει mysite βάλτε το url για το δικό σας whois/whois.php
		το οποίο κάνατε upload.
		5) Ανεβάστε το  αρχείο whois.json στον server ,στο μονοπάτι /resources/domains/
		όπως αναφέρει η whmcs εδώ http://docs.whmcs.com/WHOIS_Servers



Domain Additional Fields
-----
.. code-block:: bash

    Για να μπορεί να γίνει συμπλήρωση των extra πεδίων θα πρέπει να βάλετε
    στον φάκελο /resources/domains/ το αρχείο /resources/domains/additionalfields.php .
    - Ο διακριτικός τίτλος εταιρείας είναι ένα extra πεδίο για τις κατοχυρώσεις του μητρώου των
    ελληνικών ονομάτων χώρου.
    - Η Ιθαγένεια του ιδιοκτήτη είναι σε κάποιες περιπτωσεις υποχρεωτική για τα Eu domains.
    Περισσότερες πληροφορίες εδώ: https://eurid.eu/en/register-a-eu-domain/brexit-notice/

Lang Overrides
-----
.. code-block:: bash

	Θα πρέπει να προσθέσετε τον φάκελο overrides/ μέσα στον φάκελο /lang/ ώστε να σας εμφανίζονται κάποια επιπλέον lnagstrings
    όπως για παράδειγμα η περιγρφή των additional fields.

Οι δυνατότητες που ακολουθούν (HOOKS,SYΝCHRONIZATION) μπορούν να εφαρμοστούν στο site σας μόνο αν
έχετε whmcs version από 5.1.4 και πάνω και php από 4 και πάνω.

HOOKS
-----
.. code-block:: bash

	Αν επιθυμείτε κάθε φορά που ολοκληρώνεται μια κατοχύρωση ή μια ανανέωση ενός ονόματος
	χώρου να αλλάζουν και τα expiry_date και next_renew_date των ονομάτων στη δική σας βάση,
	τότε μπορείτε να χρησιμοποιήσετε το αρχείο domainregistrationhook.php που βρίσκεται 
	στον φάκελο hookfile:
	1) Για να λειτουργήσει το hook θα πρέπει να έχετε ενεργοποιήσει τη δυνατότητα χρησιμοποίησης
	του api του whmcs μέσα από το site σας. Για να το κάνετε αυτό ακολουθήστε τις οδηγίες που βρίσκονται εδώ:
	https://developers.whmcs.com/api/authentication/ στην παράγραφο "Authenticating With Login Credentials"
	2) Ανοίξτε το αρχείο domainregistrationhook.php και βάλτε μέσα στον κώδικα τα εξής:
	API URL συνήθως είναι (https://www.mysite.gr/whmcs/includes/api.php)
	API_USERNAME (είναι το admin username σας)
	API_PASSWORD (είναι το admin password σας)
	apikey (σας παρέχεται από το papaki)
	3) Ανεβάστε το αρχείο domainregistrationhook.php στον φάκελο includes/hooks
	4) Κάντε μια κατοχύρωση για να δείτε αν η ημερομηνία λήξης του domain στη δικό σας site είναι σωστή



SYNCHRONIZATION
---------------
.. code-block:: bash

	Η WHMCS μπορεί να συγχρονίζει τα expiry_date και next_renew_date σύμφωνα με το papaki.
	Αν θέλετε να ενεργοποιήσετε ένα cron job, το οποίο κάθε φορά που θα τρέχει θα ψάχνει 
	όλα τα pending transfer domains κι αν έχει ολοκληρωθεί η μεταφορά τους τότε τα κάνει 
	active και τους αλλάζει την ημερομηνία λήξης τους στο whmcs, ενώ επίσης θα κοιτάζει 
	τις διαφορές ανάμεσα στις ημερομηνίες των ενεργών ονομάτων και θα τις συγχρονίζει,
	θα πρέπει να κάνετε τα εξής:
	
	1) Αρχικά να κάνετε τις ρυθμίσεις που θέλετε για το SYNCHRONIZATION όπως αναφέρεται εδώ:
	https://docs.whmcs.com/Domain_Synchronisation
	Δηλαδή, θα πρέπει να πάτε στο Setup > General Settings > Domains tab και να επιλέξετε αυτά 
	που θέλετε στις επιλογές:
	α)Domain Sync Enabled - Πρέπει να είναι τσεκαρισμένο για να λειτουργεί το SYNCHRONIZATION.
	β)Sync Next Due Date - Πρέπει να είναι τσεκαρισμένο αν θέλετε να ανανεώνονται και τα next due dates, 
	όπως τα expiry πεδία.
	γ)Domain Sync Notify Only - Πρέπει να είναι τσεκαρισμένο αν δε θέλετε να ανανεώνονται αυτόματα οι 
	ημερομηνίες, απλά να στέλνεται ένα ενημερωτικό email στους admins.
	
	2) Σύμφωνα με το παρακάτω link
	http://docs.whmcs.com/Domains_Tab#Domain_Sync_Enabled
	θα πρέπει να ενεργοποιήσετε το cron
	php -q /path/to/home/public_html/whmcspath/crons/domainsync.php
	
	Σας προτείνουμε να το ενεργοποιήσετε να τρέχει μια φορά την ημέρα, γιατί μπορείτε να στείλετε requests
	για μέχρι 100 ονόματα χώρου την ημέρα ώστε να μην έχετε κάποιο πρόβλημα στο account σας.
	


TEST ENVIRONMENT
----------------

.. code-block:: bash

	Αν θέλετε το Module της whmcs να δουλεύει στο test environment τότε θα πρέπει να κάνετε τα εξής:
	Από το configuration-> domain registrars->registrar settings επιλέξτε το papaki 
	και στη συνέχεια συμπληρώστε τo test apiKey που έχετε στο Papaki.gr και σαν 
	PostUrl το https://api-test.papaki.com/register_url2.aspx.
	


 

System Requirements
-------------------
* Το   APIKey είναι απαραίτητο για να καλέσετε το  API του Papaki



Copyright
---------
Papaki
