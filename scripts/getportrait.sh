#!/bin/sh

scripts=${HOME}/scripts/
source="${scripts}data"
cd $source

if [ "$#" = "0" ]; then
  echo "USAGE:"
  echo "$0 [-o] [link1 [link1 [link3...]]]"
  echo " -o : Overwrite existing portraits."
  echo " link* : Url to portraits pack"
  exit 0
fi

overwrite=""
while [ "$#" != "0" ]; do
  case $1 in
    -o)
       overwrite="-o"
       ;;
    *) wget $1
       ;;
  esac

  shift
done

cd $scripts

./addportraits.sh $overwrite
./makepackages.sh
