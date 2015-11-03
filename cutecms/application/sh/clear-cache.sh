#!/bin/bash

path=`dirname $0 | xargs readlink -e`/../var/cache/

rm -f $path/plugin*

rm -f $path/zend*

