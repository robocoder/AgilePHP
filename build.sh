#!/bin/bash

rm -rf build
mkdir build
cp -R src/* build

# Delete SVN files
rm -rf `find build -type d -name .svn`