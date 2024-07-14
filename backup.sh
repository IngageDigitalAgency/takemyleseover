SITE=leasing
tdy=`date +%Y-%m-%d`
mysqldump $SITE -u test -p > $SITE.sql
zip -r --exclude ./images/* $SITE-$tdy.zip *
