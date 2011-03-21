#!/bin/sh

# This script is meant to be used as a post-commit hook in a git repo.
# Put it in .git/hooks/post-commit and you're all set.
# (Note: It excludes the 'build' directory.)

PWD=`pwd`
DIR=`basename "$PWD"`
DROPBOX="/path/to/my/Dropbox/$DIR.tar.gz"

# move above the git repo
cd "$PWD/.."

# tar and zip it
tar -cf "$DIR.tar" --exclude "$DIR/build" "$DIR"
gzip -f "$DIR.tar" > $DROPBOX