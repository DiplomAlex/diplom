#!/usr/bin/bash

function settingsGlobal()
{
	# name of 'development' dir
	development='development'

	# name of 'production' dir
	production='production'

	productionZip='production.zip'
	installPhp='install.php'
	deployerSh='deployer.sh'

	# zendenc5 command
	zendenc5='/usr/share/Zend/ZendGuard/bin/zendenc5'

	# path from this script to root dir (where development and production dirs are placed)
	pathToProjectRoot='../../../'

	# full mysqldump command with db and username
	mysqlDumpCommand='mysqldump cutecms_polzunki -u root --skip-add-drop-table --skip-add-locks'
}

function settingsDeploy()
{
	dbHost=''
	dbName=''
	dbUser=''
	dbPassword=''

	siteDomain=''
	siteName=''
	supportEmail=''
	supportName=''

	ftpHost='hostname'
	ftpUser='username'
	ftpPassword='password'
	ftpPath='path'

	sshHost=''
	sshUser=''
	sshPassword=''
	sshPath=''
}

function deployChangeConfig()
{
	sed -i 's/resources\.db\.params\.host\ =\ localhost/resources\.db\.params\.host\ =\ '$dbHost'/'  ./$production/application/configs/application.ini
	sed -i 's/resources\.db\.params\.dbname\ =/resources\.db\.params\.dbname\ =\ '$dbName'/' 		 ./$production/application/configs/application.ini
	sed -i 's/resources\.db\.params\.username\ =/resources\.db\.params\.username\ =\ '$dbUser'/'     ./$production/application/configs/application.ini
	sed -i 's/resources\.db\.params\.password\ =/resources\.db\.params\.password\ =\ '$dbPassword'/' ./$production/application/configs/application.ini

	sed -i 's/domain\ =\ \"\"/domain\ =\ "'$siteDomain'"/'            ./$production/application/modules/kernel/configs/config.ini
	sed -i 's/siteName\ =\ \"\"/siteName\ =\ "'$siteName'"/'          ./$production/application/modules/kernel/configs/config.ini
	sed -i 's/supportName\ =\ \"\"/supportName\ =\ "'$supportName'"/' ./$production/application/modules/kernel/configs/config.ini
	sed -i 's/support\ =\ \"\"/support\ =\ "'$supportEmail'"/'        ./$production/application/modules/kernel/configs/config.ini
}

function compileI18n()
{
	msgfmt -o ./$development/cutecms/application/i18n/ru/kernel.mo   ./$development/cutecms/application/i18n/ru/kernel.po
	msgfmt -o ./$development/cutecms/application/i18n/ru/social.mo   ./$development/cutecms/application/i18n/ru/social.po
	msgfmt -o ./$development/cutecms/application/i18n/ru/catalog.mo  ./$development/cutecms/application/i18n/ru/catalog.po
	msgfmt -o ./$development/cutecms/application/i18n/ru/checkout.mo ./$development/cutecms/application/i18n/ru/checkout.po
	msgfmt -o ./$development/application/i18n/ru/shop.mo ./$development/application/i18n/ru/shop.po
}

function encodeScripts()
{
	$zendenc5 --silent --delete-source ./$production/application/modules/kernel/BootstrapInstaller.php
	$zendenc5 --silent --delete-source ./$production/application/modules/kernel/controllers/InstallerController.php
	$zendenc5 --silent --delete-source ./$production/application/modules/kernel/models/Service/Installer.php

	$zendenc5 --silent --delete-source ./$production/application/modules/kernel/Bootstrap.php

	$zendenc5 --silent --delete-source --recursive ./$production/application/library/App/Event
	$zendenc5 --silent --delete-source --recursive ./$production/application/library/App/Model
	$zendenc5 --silent --delete-source ./$production/application/library/App/Event.php
	$zendenc5 --silent --delete-source ./$production/application/library/App/View/Helper/FormJqGrid.php
	$zendenc5 --silent --delete-source ./$production/application/library/App/View/Helper/FormFlexiGrid.php

	$zendenc5 --silent --delete-source ./$production/application/modules/catalog/models/Mapper/Db/Attribute.php
	$zendenc5 --silent --delete-source ./$production/application/modules/catalog/models/Service/Attribute.php


	$zendenc5 --silent --delete-source ./$production/application/modules/checkout/controllers/OrderController.php
	$zendenc5 --silent --delete-source ./$production/application/modules/checkout/models/Mapper/Db/Order.php
	$zendenc5 --silent --delete-source ./$production/application/modules/checkout/models/Service/Order.php
}


function prepareProductionDir()
{
	#dump db
	rm -rf ./$development/_sql/*
	$mysqlDumpCommand > ./$development/_sql/db.sql

	# compile translations
	compileI18n

	# clear cache
	./$development/application/sh/clear-cache.sh

	# remove temp data in development
	rm -f ./$development/tmp/captcha/*

	# remove old production preparation
	rm -rf ./$production
	rm -f  ./$productionZip
	rm -f  ./$installPhp

	# prepare production dir
	cp -rL ./$development ./$production

	# remove found recursive
	find ./$production -name .svn -print0 | xargs -0 rm -rf
	find ./$production -name .git -print0 | xargs -0 rm -rf
	find ./$production -name todo_curr -print0 | xargs -0 rm -rf

	# remove unnecessary
	rm -f  ./$production/application/var/log/*
	rm -f  ./$production/.project
	rm -rf ./$production/.settings
	rm -rf ./$production/js/ckfinder/_samples
	rm -rf ./$production/js/ckeditor/_samples
	rm -rf ./$production/js/ckeditor/_source
	rm -rf ./$production/application/tests
	rm -rf ./$production/cutecms/application/tests
	rm -f  ./$production/application/sh/$deployerSh
	rm -f  ./$production/cutecms/application/sh/$deployerSh
	rm -f  ./$production/cutecms/application/sh/prepare-po.sh
	rm -f  ./$production/$installPhp
	rm -f  ./$production/cutecms/$installPhp
	rm -rf  ./$production/cutecms/tmp
	rm -rf  ./$production/cutecms/uploads
	rm -f  ./$production/cutecms/index.php
	rm -rf  ./$production/cutecms/_sql
	rm -rf  ./$production/cutecms/js
	rm -rf  ./$production/cutecms/skins
	rm -f  ./$production/cutecms/.htaccess.sample


	# prepare ini's
	if [ -e ./$production/application/configs/application.ini.production ]; then
		cp -f  ./$production/application/configs/application.ini.production           ./$production/application/configs/application.ini
	fi
	if [ -e ./$production/application/modules/shop/configs/config.ini.production ]; then
		cp -f  ./$production/application/modules/shop/configs/config.ini.production ./$production/application/modules/shop/configs/config.ini
	fi
	rm -f  ./$production/application/configs/application.ini.production           ./$production/application/configs/application.ini.development
	rm -f  ./$production/application/modules/shop/configs/config.ini.production ./$production/application/modules/shop/configs/config.ini.development


	# switch to production mode
	sed -i 's/development/production/' ./$production/index.php

	# lets some zend
#	encodeScripts
}


function makeArchive()
{
	#preparation
	prepareProductionDir

	rm -f ./$production/application/var/etc/installation.xml

	# make production zip
	zip -q -r $productionZip $production

	# base64 encode this zip
	base64 $productionZip > $productionZip.enc

	cat ./$development/cutecms/$installPhp ./$productionZip.enc > ./$installPhp

	rm -f ./$productionZip.enc
    rm -f ./$productionZip

	echo 'Preparation successfull! Archive-file: '`pwd`'/'$installPhp

}


function deploy()
{

	settingsDeploy

	#preparation
	prepareProductionDir
	rm -f ./$production/application/modules/kernel/models/Service/Installer.php


	# change params in ini's
	deployChangeConfig

	inst=$1

	if [ "$inst" != 'install' ]; then
	    rm -rf $production/tmp $production/uploads
	fi

	zip -q -r $productionZip $production

	echo "Local $productionZip prepared"

	lftp $ftpHost -u $ftpUser,$ftpPassword -e "put -O $ftpPath $productionZip && quit"

	echo "$productionZip transmitted by ftp"

	rmtc="
				       cd    "$sshPath"
				    && unzip -q    "$productionZip"
				    && chmod a+rwx "$production"/application/var/cache
				    && chmod a+rwx "$production"/application/var/log
				    && chmod a+rwx "$production"/application/var/tmp
				    && chmod a+rwx "$production"/application/var/etc
				    && chmod a+rw  "$production"/application/var/etc/*
	     "
	if [ "$inst" == 'install' ]; then
	    rmtc=$rmtc"
				    && chmod a+rwx "$production/"tmp "$production"/tmp/captcha
				    && chmod a+rwx "$production"/uploads "$production"/uploads/png
				    && chmod a+rwx "$production"/uploads/ckfinder "$production"/uploads/ckfinder/images
				    && rm    -f    .htaccess
		      "
	else
		rmtc=$rmtc"
				    && cp    -f   application/var/etc/html_meta.xml "$production"/application/var/etc/html_meta.xml
				    && chmod a+rw "$production"/application/var/etc/html_meta.xml
		"
	#    rmtc=$rmtc"
	#			    && mv    application _bak.application
	#			    && mv    sh _bak.sh
	#			    && mv    js _bak.js
	#			    && mv    skins _bak.skins
	#	      "
	    rmtc=$rmtc"
				    && rm    -rf application
				    && rm    -rf sh
				    && rm    -rf js
				    && rm    -rf skins
		      "
	fi
	rmtc=$rmtc"
				    && mv    "$production"/application ./
				    && mv    "$production"/sh ./
				    && mv    "$production"/js ./
				    && mv    "$production"/skins ./
				    && mv    "$production"/index.php ./
				    && mv    "$production"/.htaccess ./
		  "
	if [ "$inst" == 'install' ]; then
	    rmtc=$rmtc"
				    && mv    "$production"/tmp ./
				    && mv    "$production"/uploads ./
		      "
	fi
	rmtc=$rmtc"		&& rm    -rf "$production"
				    && rm    -f  "$productionZip"
		  "

	plink -pw $sshPassword $sshUser@$sshHost $rmtc

	echo 'Remote commands complete. Deployment finished successfull!'

}

# in eclipse :  create external tool
#               Location   : .../sh/deployer.sh
#               Working dir: .../sh
#               Arguments  : ftpupload ${resource_name} ${container_path} ${project_name} 
function ftpupload()
{
    settingsDeploy
    cd $development
    fname=$1
    relPath=$2
    projName=/$3
    path=${relPath:${#projName}}
    
    if [ "$ftpPath$path" == '' ]; then
	ftpPath=/
    fi

    echo running: ncftpput -u $ftpUser -p $ftpPassword $ftpHost $ftpPath$path .$path/$fname
    ncftpput -u $ftpUser -p $ftpPassword $ftpHost $ftpPath$path .$path/$fname
    echo finished!
}


################# enter point ##########################################

settingsGlobal

cd $pathToProjectRoot

action=$1

if [ "$action" == 'archive' ]; then
	makeArchive
elif [ "$action" == 'install' ]; then
	deploy install
elif [ "$action" == 'update' ]; then
	deploy update
elif [ "$action" == 'ftpupload' ]; then
	ftpupload $2 $3 $4
else
	echo "usage: deployer.sh [archive|install|update|ftpupload]"
fi


