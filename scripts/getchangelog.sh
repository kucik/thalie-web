#!/bin/bash

cd /home/nwn/src/thalie-scripts/; 
/usr/bin/git log -n 20 --date=iso --no-merges --pretty=format:"%ad s %x09%s" --date=iso > /var/www/thalie/data/changelog-scripts; 

cd /home/nwn/src/thalie/; /usr/bin/git log -n 20 --date=iso --no-merges --pretty=format:"%ad t %x09%s" --date=iso > /var/www/thalie/data/changelog-th; 

cd /var/www/thalie/data/; 
/bin/cat changelog-scripts newline changelog-th | /usr/bin/sort -r | /usr/bin/head -n 20 > changelog

