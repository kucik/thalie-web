#!/bin/sh

portrait="$1"
portraitsdir="$HOME/images/portraits/"
packdir="$HOME/data/portraitspacks/"
scriptdir="${HOME}/scripts/"

cd "$portraitsdir"
for img in `ls *m.tga`; do
  name=`echo "$img" | sed -e 's/m\.tga$//'`
  if [ ! -e  "${packdir}${name}_pack.zip" ]
  then
    echo "Creating ${packdir}${name}_pack.zip"

    ${scriptdir}packportrait.sh "${name}"
  fi
  if [ ! -e "${name}m.jpg" ]
  then
     echo "Creating ${name}m.jpg"
    mogrify -format jpg ${name}m.tga
  fi
  

done

#echo "Packing portraits $portrait*.tga"
#zip "${packdir}${portrait}_pack.zip" "${portrait}h.tga" "${portrait}l.tga" "${portrait}m.tga" "${portrait}s.tga" "${portrait}t.tga"

#mv "${portrait}_pack.zip" "$packdir"
#mogrify -format jpg *m.tga
