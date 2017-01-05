#!/bin/sh

#ln_cut="28800"
ln_cut="47668"
logfile="/var/www/thalie/stats/logs/vmstat.log"
memlog="/var/www/thalie/stats/logs/memstats.log"

oldpid=`head -n 1 /tmp/vmstatlog.pid 2> /dev/null`
echo $oldpid

if [ -n "$oldpid" ] 
then
  kill $oldpid
fi

sleep 10 
mv $logfile $logfile.tmp
tail -n $ln_cut $logfile.tmp > $logfile

run=1
mypid=$$
echo $mypid > /tmp/vmstatlog.pid

while [ "$run" -gt "0" ]
do
  nwstat=`ps -C nwserver -o %cpu,%mem | grep -v CPU` 
  stat=`vmstat 1 2 | tail -n 1`; 
  echo "`date "+%Y/%m/%d %H:%M:%S" `$stat $nwstat";
  i=$(($i + 1));
  mem=`echo $stat | tr -s ' ' |cut -d ' ' -f 4`
  echo Mem:$mem >&2
  if [ $mem -lt 50000 ]
     then
     date "+%Y/%m/%d %H:%M:%S" >> $memlog
     ps -Ao %mem,%cpu,pid,ppid,user,fname,tmout,f,wchan | sort | tail -n 10 >> $memlog
  fi
  sleep 5
done  >> $logfile
