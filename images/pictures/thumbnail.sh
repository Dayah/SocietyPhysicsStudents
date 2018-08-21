#! /bin/bash

for i in *.JPG; do
	convert "$i" -thumbnail 200 "../thumbnails/$i";
done;
