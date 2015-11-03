#!/bin/bash

#
# scans directories recursively starting from current and collects all messages
# from php and phtml files wrapped to _() or translate()
#
# result is "messages.po" file
#

path=$1

# find files
find $path \( -name '*.php' -o -name '*.phtml' \) > files.lst

# prepare po
xgettext -L PHP -f files.lst -o msg.po --from-code='UTF-8'
xgettext -L PHP -f files.lst -o msg.po --from-code='UTF-8' -j msg.po -x msg.po --keyword=translate

# fix encoding and language
sed -i 's/charset\=CHARSET/charset\=UTF\-8/' msg.po
sed -i 's/Language\-Team\:\ LANGUAGE/Language\-Team\:\ ru/' msg.po

# uniq messages
msguniq -t 'UTF-8' -o messages.po msg.po

# clear trash
rm files.lst msg.po

