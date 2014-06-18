#!/bin/sh
# Create a local directory on your machine to house
# a copy of the repository.

mkdir WordPress

# Check out the repository
svn co http://plugins.svn.wordpress.org/unisender-integration WordPress

# As you can see, subversion has added ( "A" for "add" )
# all of the directories from the central repository to
# your local copy.

# Copy the plugin files to the local copy.
# Put everything in the trunk/ directory for now.
cd WordPress/
cp ~/my-plugin.php trunk/my-plugin.php
cp ~/readme.txt trunk/readme.txt

# Let subversion know you want to add those new files
# back into the central repository.
svn add trunk/*

# Now check in the changes back to the central repository.
# Give a message to the check in.
svn ci -m 'Adding first version of my plugin'