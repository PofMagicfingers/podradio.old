#!/bin/bash
#
# m4a to wav
faad "$1"
x=`echo "$1"|sed -e 's/.m4a/.wav/'`
y=`echo "$1"|sed -e 's/.m4a/.mp3/'`
faad -i "$1" 2>.trackinfo.txt
title=`grep 'title: ' .trackinfo.txt|sed -e 's/title: //'`
artist=`grep 'artist: ' .trackinfo.txt|sed -e 's/artist: //'`
album=`grep 'album: ' .trackinfo.txt|sed -e 's/album: //'`
genre=`grep 'genre: ' .trackinfo.txt|sed -e 's/genre: //'`
track=`grep 'track: ' .trackinfo.txt|sed -e 's/track: //'`
year=`grep 'year: ' .trackinfo.txt|sed -e 's/year: //'`
lame --alt-preset 160 --tt "$title" --ta "$artist" --tl "$album" --tg "$genre" --tn "$track" --ty "$year" "$x" "$y"
rm .trackinfo.txt
rm "$x"
done
