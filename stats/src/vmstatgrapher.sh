#!/bin/sh

ln_cut="8800"
logfile="vmstat_daily.log"
cd /var/www/thalie/stats/logs

tail -n $ln_cut vmstat.log > $logfile

NUM_RECORDS=`cat  $logfile | wc -l`
#zjistime rozsahy grafu
#MAX_CS=`cat $logfile | tr -s ' ' | cut -f 13 -d ' ' | sort -n -r | head -n 1);
#MIN_CS=$(cut -f 13 -d ' ' < $logfile | sort -n | head -n 1);
#MAX_IO_IN=$(cut -f 11 -d ' ' < $logfile | sort -n -r | head -n 1);
#MIN_IO_IN=$(cut -f 11 -d ' ' < $logfile | sort -n | head -n 1);
#MAX_IO_OUT=$(cut -f 12 -d ' ' < $logfile | sort -n -r | head -n 1);
#MIN_IO_OUT=$(cut -f 12 -d ' ' < $logfile | sort -n | head -n 1);
#MAX_IO=$(($MAX_IO_IN + $MAX_IO_OUT));
#echo $MIN_IO_IN $MIN_IO_OUT
#MIN_IO=$(($MIN_IO_IN + $MIN_IO_OUT));
START_TIME=$(head -n 1 $logfile | cut -f 1,2 -d ' ' | sed -e 's/\//\\\//g');
STOP_TIME=$(tail -n 1 $logfile | cut -f 1,2 -d ' ' | sed -e 's/\//\\\//g')
CPU_USAGE_SYS=`tail -n 1 $logfile | tr -s ' ' | cut -d ' ' -f 15`
#CPU_USAGE_SYS=`tail -n 1 $logfile | tr -s ' ' | cut -d ' ' -f 16`
CPU_USAGE_USR=`tail -n 1 $logfile | tr -s ' ' | cut -d ' ' -f 16`
#CPU_USAGE_USR=`tail -n 1 $logfile | tr -s ' ' | cut -d ' ' -f 17`
MEM_USAGE=`tail -n 1 $logfile | tr -s ' ' | cut -d ' ' -f 6`
MEM_USAGE=$(( (4063200 - $MEM_USAGE) / 1024))
CPU_USAGE_ACT=$(($CPU_USAGE_SYS + CPU_USAGE_USR)) 

cat ../cfg/gplot.conf | sed -e "s/\$logfile/$logfile/g" \
               | sed -e "s/\$NUM_RECORDS/$NUM_RECORDS/g" \
               | sed -e "s/\$START_TIME/$START_TIME/g" \
               | sed -e "s/\$STOP_TIME/$STOP_TIME/g" \
               | sed -e "s/\$CPU_USAGE_ACT/$CPU_USAGE_ACT/g" \
               | sed -e "s/\$MEM_USAGE/$MEM_USAGE/g" \
               | gnuplot 
 
#               | sed -e "s/\$MAX_CS-\$MIN_CS/$MAX_CS-$MIN_CS/g" \
#               | sed -e "s/\$MAX_IO-\$MIN_IO/$MAX_IO-$MIN_IO/g" \
#               | gnuplot 

exit 0;


