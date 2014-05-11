#!/bin/sh
php -f /etc/podradio/bin/src/update_files.php > /tmp/cmd.sh
chmod 777 /tmp/cmd.sh
/tmp/cmd.sh
chmod -R 777 /etc/podradio/content/files
rm /tmp/cmd.sh
