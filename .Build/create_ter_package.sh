#!/bin/bash

bold=$(tput bold)
normal=$(tput sgr0)

printf "\n${bold}Check if ext_emconf.php is found: ${normal}"
if [ -f ext_emconf.php ]
then
    printf "ext_emconf.php found"
else
    printf "No ext_emconf.php found. Are you in the right directory?"
    exit 1
fi

printf "\n${bold}Get version from ext_emconf.php: ${normal}"
version=`php .Build/get_version_of_extension.php`
printf $version

printf "\n${bold}Create ZIP package: ${normal}"
mkdir .Build/packages
if [ -f .Build/packages/yoast_seo_$version.zip ]
then
    rm .Build/packages/yoast_seo_$version.zip
fi
zip --exclude=*.git* --exclude=*.DS_Store* --exclude=*Gruntfile.js* --exclude=*yarn.lock* --exclude=*package.json* --exclude=*package-lock.json* --exclude=*composer.lock* --exclude=*.php_cs.cache* --exclude=*.travis.yml* --exclude=*.idea* --exclude=*node_modules* --exclude=*grunt* --exclude=*.Build* -r .Build/packages/yoast_seo_$version.zip ./
echo ""