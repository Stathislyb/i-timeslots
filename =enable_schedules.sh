#!/bin/sh
id="43 44 45 46 47"

for i in $id
do
echo "/usr/local/bin/curl -s -S 'http://vlsi.gr/schedule/enable.php?id=$i' " | at 17:00 Tue
done
