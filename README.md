# Beteseb-joomla
Joomla website backup files for Beteseb-joomla

## Steps to backup joomla site
1. Copy all files under the root folder.
   eg. in AWS lightsail using bitnami this folder is /bitnami/joomla
      in GoDaddy CPanel backup public_html
2. Remove configuration.php from the backup.
3. open phpMyAdmin and export database as SQL files.

## Steps to restore joomla site
1. on target system. stop webserver.
2. copy backed up files to joomla root folder.
3. adjust permissions if needed so the contents in the folder are writable.
4. run the exported sql to restore the database
5. in configuration.php set the correct username, pwd etc.

## Troubleshooting
1. Joomla site after restore is down and shows error page.
   - open configuration.php
   - set debug=true and error_reporting to maximum.
   - stop and restart webserver.
