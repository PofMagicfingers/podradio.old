#!/bin/sh

cd /etc/podradio/auto_update/internal
php -f internal_rss.php > cmd.sh
chmod 777 ./cmd.sh
./cmd.sh
rm cmd.sh
