#!/bin/bash

apt-get update
apt-get install -y apache2

cp /var/www/provision/config/apache/vhosts/cryptobots.local.conf /etc/apache2/sites-available/cryptobots.local.conf
cp /var/www/provision/config/apache/vhosts/api-cryptobots.local.conf /etc/apache2/sites-available/api-cryptobots.local.conf
cp /var/www/provision/config/apache/vhosts/view-cryptobots.local.conf /etc/apache2/sites-available/view-cryptobots.local.conf

a2dissite 000-default

a2ensite cryptobots.local.conf
a2ensite api-cryptobots.local.conf
a2ensite view-cryptobots.local.conf

a2enmod rewrite
service apache2 restart

# ssl certificate for domain
sudo apt install openssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/cryptobots.local.key \
    -out /etc/ssl/certs/cryptobots.local.crt \
    -subj "/C=US/ST=CA/L=San Francisco/O=Example, Inc./OU=IT Department/CN=cryptobots.local"

# ssl certificate for api domain
sudo apt install openssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/api-cryptobots.local.key \
    -out /etc/ssl/certs/api-cryptobots.local.crt \
    -subj "/C=US/ST=CA/L=San Francisco/O=Example, Inc./OU=IT Department/CN=api-cryptobots.local"

# ssl certificate for api domain
sudo apt install openssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/view-cryptobots.local.key \
    -out /etc/ssl/certs/view-cryptobots.local.crt \
    -subj "/C=US/ST=CA/L=San Francisco/O=Example, Inc./OU=IT Department/CN=view-cryptobots.local"

sudo a2enmod ssl
sudo systemctl restart apache2
