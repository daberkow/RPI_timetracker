RPI_timetracker - Dan Berkowitz - February 2014
===============

TROUBLE SHOOTING:
Certs:
    If all of a sudden there is a error logging into CAS. Goto http://curl.haxx.se/ca/, download the latest CACERT.pem
    and place it into the cas folder.

Rebuild database:
If you just drop the databse structor into a database, you will have to create a page labeled called "home" that does the index.
make a page called "homeAuth" for authenticed home users. And give a user privilege 2 in the users table so they can create a 

v1.00 - fixed bugs
v1.01 - bug fixes
v1.02 - Gui Updates
v1.03 - Gui Updates, database integrity updates, jquery update, cert update
	-Ajax.php had limits added to the punch area, so that if error data got into db, only one will be changed going forward
	-Added a ability for gorup admins to purge user data

Never completed features:
    Basic Version
        -Started and never finished
    Export Import
        -Started working on this export worked fine, never imported correctly
    Email reminders
        -Server never finished
    Ctrl/Shift Click
        -Was not highlighting correct boxes
