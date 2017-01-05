#!/bin/sh

portrait="$1"
portraitsdir="$HOME/images/portraits/"
packdir="$HOME/data/portraitspacks/"

cd "$portraitsdir"

echo "Packing portraits $portrait*.tga"
zip "${packdir}${portrait}_pack.zip" "${portrait}h.tga" "${portrait}l.tga" "${portrait}m.tga" "${portrait}s.tga" "${portrait}t.tga"
#zip "${packdir}portraits_all.zip" "${portrait}h.tga" "${portrait}l.tga" "${portrait}m.tga" "${portrait}s.tga" "${portrait}t.tga"

#mv "${portrait}_pack.zip" "$packdir"
