#!/bin/sh

portrait="$1"
resman_dir="/home/nwn/resman/"
packdir="$HOME/download/dmareas/"
scriptdir="${HOME}/scripts/"

packarea() {
  name="$1"
  cd ../
  echo zip ${packdir}${name}.zip are/${name}.are gic/${name}.gic git/${name}.git
  zip --junk-paths ${packdir}${name}.zip are/${name}.are gic/${name}.gic git/${name}.git
  cd -
}


cd "$resman_dir/are/"
for area in `ls *.are 2>/dev/null`; do
  name=`echo "$area" | sed -e 's/.are$//'`
  if [ ! -e  "${packdir}${name}.zip" ]
  then
    echo "Creating ${packdir}${name}.zip"
    packarea ${name}
##    ${scriptdir}packportrait.sh "${name}"
  fi

done

#echo "Packing portraits $portrait*.tga"
#zip "${packdir}${portrait}_pack.zip" "${portrait}h.tga" "${portrait}l.tga" "${portrait}m.tga" "${portrait}s.tga" "${portrait}t.tga"

#mv "${portrait}_pack.zip" "$packdir"
#mogrify -format jpg *m.tga
