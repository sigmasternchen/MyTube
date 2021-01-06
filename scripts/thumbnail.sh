#!/bin/sh

dir="content/$1/"
mkdir -p "$dir"

ffmpeg -i "landingzone/$1.vid" -vf  "thumbnail,scale=640:360" -frames:v 1 "$dir/thumb.png"