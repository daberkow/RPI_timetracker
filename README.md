RPI_timetracker - Dan Berkowitz - February 2014
===============

Current Bugs: If your time zone is not eastern, then it messes up the dates when drawing in javascripts

TROUBLE SHOOTING:
Certs:
    If all of a sudden there is a error logging into CAS. Goto http://curl.haxx.se/ca/, download the latest CACERT.pem
    and place it into the cas folder.

Rebuild database:
If you just drop the database structure into a database, you will have to create a page labeled called "home" that does the index.
make a page called "homeAuth" for authenticated home users. And give a user privilege 2 in the users table so they can create a 

v1.00 - fixed bugs
v1.01 - bug fixes
v1.02 - Gui Updates
v1.03 - Gui Updates, database integrity updates, jquery update, cert update
	-Ajax.php had limits added to the punch area, so that if error data got into db, only one will be changed going forward
	-Added a ability for group admins to purge user data
v1.04 - Fixed Error in core for group_settings, fixed mysql_escape used
v1.05 - Fixed change week draw bug
v1.06 - Admins changing date-zones under other users could not load other dates data in timecard view

Never completed features:
    Basic Version
        -Started and never finished
    Export Import
        -Started working on this export worked fine, never imported correctly
    Email reminders
        -Server never finished
    Ctrl/Shift Click
        -Was not highlighting correct boxes
