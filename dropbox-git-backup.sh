#!/bin/sh

PWD=`pwd`
DIR=`basename "$PWD"`
DROPBOX="/path/to/my/Dropbox/$DIR.tar.gz"

# move above the git repo
cd "$PWD/.."

# tar and zip it
tar -cf "$DIR.tar" --exclude "$DIR/build" "$DIR"
gzip -f "$DIR.tar" > $DROPBOX