chown -R ian:developers *
chown -R apache:developers logs
chmod 777 logs
chmod 666 -R logs/*
chown -R apache:developers images
chown -R apache:developers files
chmod -R g=wrx admin/frontend/forms/*
