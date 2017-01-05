#!/bin/sh

db_host="" # FIXME FILL LOGIN DATA
db_user=""
db_name=""
db_pass=""

#db_host="89.233.180.146"
#db_user="thalie"
#db_pass="thdbp455"

ln_cut="440"
logfile="/var/www/thalie/stats/logs/ingamestat.log"
deathlogfile="/var/www/thalie/stats/logs/deathlog"

SQL="SELECT count(*)
 FROM location_property\
 WHERE boss_spawn_time !=0 AND 
       boss_spawn_time < CAST( (\
                                SELECT val\
                                FROM pwdata\
                                WHERE name = 'CURRENT_TIMESTAMP'\
                                ) AS DECIMAL );"

bosses=`mysql --database=$db_name -u $db_user --password=$db_pass -e "$SQL" -h $db_host | tr -d '\n' | tr -d -c '[:digit:]'`

SQL="SELECT count(*)
 FROM dump;" 
players=`mysql --database=$db_name -u $db_user --password=$db_pass -e "$SQL" -h $db_host | tr -d '\n' | tr -d -c '[:digit:]'`

mysql --database=$db_name -u $db_user --password=$db_pass -e "select  date, level, deathlog.*, 'line' as header from deathlog WHERE killer_acc != 'NPC' AND DATE_SUB(NOW(),INTERVAL 3 DAY) < date;" | tr '\t' ' ' | grep 'line$' > ${deathlogfile}.log 
mysql --database=$db_name -u $db_user --password=$db_pass -e "select  date, level, deathlog.*, 'line' as header from deathlog WHERE killer_acc = 'NPC' AND DATE_SUB(NOW(),INTERVAL 3 DAY) < date;" | tr '\t' ' ' | grep 'line$' > ${deathlogfile}_npc.log

echo `date "+%Y/%m/%d %H:%M:%S"` "$bosses $players" >> ${logfile}.temp
tail -n $ln_cut ${logfile}.temp >  ${logfile}

NUM_RECORDS=`cat  $logfile | wc -l`
START_TIME=$(head -n 1 $logfile | cut -f 1,2 -d ' ' | sed -e 's/\//\\\//g');
STOP_TIME=$(tail -n 1 $logfile | cut -f 1,2 -d ' ' | sed -e 's/\//\\\//g')
BOSSESS_ACT=`tail -n 1 $logfile | tr -s ' ' | cut -d ' ' -f 3`
eval=`cat $logfile | tr -s ' ' | cut -d ' ' -f 3 | grep "[0-9]" | tr '\n' '+' | sed -e 's/+$//' `
cnt=`echo "$eval" | tr -d -c '+' | wc -m`
BOSSESS_AVG=$(( ($eval) / ($cnt + 1)  ))

PLAYERS_ACT=`tail -n 1 $logfile | tr -s ' ' | cut -d ' ' -f 4`
eval=`cat $logfile | tr -s ' ' | cut -d ' ' -f 4 | grep "[0-9]" | tr '\n' '+' | sed -e 's/+$//' `
cnt=`echo "$eval" | tr -d -c '+' | wc -m`
PLAYERS_AVG=$(( ($eval) / ($cnt + 1)  ))
PLAYERS_MAX=`cat $logfile | tr -s ' ' | cut -d ' ' -f 4 | sort -n| tail -n 1`

DEATHLOG_STARTTIME=`head -n 1 ${deathlogfile}_npc.log | cut -d ' ' -f 1,2`
#DEATHLOG_STOPTIME=`tail -n 1 $deathlogfile | cut -d ' ' -f 1,2`
DEATHLOG_STOPTIME=`date +"%Y-%m-%d %H:%M:%S"`

cd /var/www/thalie/stats/logs
log="ingamestat.log"
cat ../cfg/ingamegplot.conf | sed -e "s/\$logfile/$log/g" \
               | sed -e "s/\$NUM_RECORDS/$NUM_RECORDS/g" \
               | sed -e "s/\$START_TIME/$START_TIME/g" \
               | sed -e "s/\$STOP_TIME/$STOP_TIME/g" \
               | sed -e "s/\$BOSSESS_ACT/$BOSSESS_ACT/g" \
               | sed -e "s/\$BOSSESS_AVG/$BOSSESS_AVG/g" \
               | sed -e "s/\$PLAYERS_ACT/$PLAYERS_ACT/g" \
               | sed -e "s/\$PLAYERS_AVG/$PLAYERS_AVG/g" \
               | sed -e "s/\$PLAYERS_MAX/$PLAYERS_MAX/g" \
               | sed -e "s/\$DEATHLOG_STARTTIME/$DEATHLOG_STARTTIME/g" \
               | sed -e "s/\$DEATHLOG_STOPTIME/$DEATHLOG_STOPTIME/g" \
               | gnuplot

