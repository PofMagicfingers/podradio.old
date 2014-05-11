#!/bin/sh

cd /etc/podradio/auto_update
php -f rss.php > cmd.sh
chmod 777 ./cmd.sh
./cmd.sh
rm cmd.sh
