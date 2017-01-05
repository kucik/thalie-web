#!/bin/sh

dest=${HOME}/images/portraits/
scripts=${HOME}/scripts/

overwrite="0"

while [ "$#" != "0" ]; do
  case $1 in
    -o)
       overwrite=1
       ;;
  esac

  shift
done


source="${scripts}data"
cd $source

for arch in `ls *.zip`; do
 unzip $arch
done

rm *.zip

for arch in `ls *.rar`; do
 unrar x $arch
done

rm *.rar

for arch in `ls file.php\?id\=* `; do
  if unrar x "$arch"; then
    rm "$arch"
  else 
    if unzip "$arch"; then
      rm "$arch"
    else 
      echo "Error! Cannot unpack $arch. Unknown compresion.";
    fi
  fi 
done 

###########
## enter subdirectories
######################
for dir in `ls -l | grep "^d" | sed -e 's/.* //g'`; do
  echo "Moving from subdirectory $dir"
  mv $dir/* .
  rmdir "$dir"
done

echo $source | grep /$ > /dev/null
if [ $? -eq 1 ]; then
source=$source/
fi

cd ../

for i in $( ls $source ); do
#  echo "$i"
  if `echo "$i" | grep -vi ".tga" > /dev/null`; then
   continue
  fi
  if [ ! -e "${source}${i}" ] 
  then
    echo "!!invalid file '$i'"
    continue
  fi
  name=`echo $i |tr A-Z a-z`
  if [ "$overwrite" -eq "0" ] && [ -e "$dest$name" ]
  then
    echo "File $dest$name already exists"
    rm "$source$i"
    continue
  fi
  mv $source$i $dest$name
  echo "$source$i -> $dest$name"
#  chgrp games $dest$name
  chmod ug+rw $dest$name
done
