#!/bin/bash

# using: cli-service.sh export user
#        cli-service.sh export document
#        cli-service.sh import user
#        cli-service.sh import document


# absolute path absolutely required for cron
path=`dirname $0 | xargs readlink -e`/..

# import or export
action=$1

# user or document
model=$2

if [ $action = "import" ]; then
    if [ $model = "category" ]; then
        params="catalog/admin-category/import"
    elif [ $model = "item" ]; then
	    params="catalog/admin-item_index/import"
	fi
elif [ $action = "export" ]; then
	if [ $model = "category" ]; then
		params="catalog/admin-category/export"
	elif [ $model = "item" ]; then
		params="catalog/admin-item/export"
	fi
elif [ $action = "send-emails" ]; then
		params="admin-email-queue/send-top"
fi

/usr/bin/php $path/index.php $params

