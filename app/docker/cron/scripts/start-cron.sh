#!/usr/bin/env sh

/usr/sbin/crond

trap "pkill -15 -f 'crond'; exit" SIGINT SIGTERM SIGQUIT

tail -f /var/log/cron.log & wait $!
