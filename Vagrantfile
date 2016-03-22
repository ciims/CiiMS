# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

  # Use a preconfigured Vagrant box
  config.vm.box = "charlesportwoodii/php7_trusty64"
  config.vm.box_check_update = true

  config.vm.synced_folder ".", "/var/www", 
    id: "vagrant-root",
    owner: "vagrant", 
    group: "www-data", 
    mount_options: ["dmode=775,fmode=775"]

  config.vm.provision "shell", inline: <<-SHELL, privileged: false
    # Upgrade PHP & Nginx
    echo "Upgrading web server packages"
    sudo apt-get update
    sudo apt-get install -y php-fpm-7.0 nginx-mainline git
    sudo ldconfig

    # Update the user's path for the ~/.bin directory

    export BINDIR="$HOME/.bin"
    if [[ ! -d "${BINDIR}" ]]
    then
      # Add ~/.bin to PATH and create the ~/.bin directory
      echo "export PATH=\"\$PATH:\$HOME/.bin\"" >> /home/vagrant/.bashrc
      mkdir -p /home/vagrant/.bin
      chown -R vagrant:vagrant /home/vagrant/.bin

      # Install Composer
      php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
      php -r "if (hash('SHA384', file_get_contents('composer-setup.php')) === '41e71d86b40f28e771d4bb662b997f79625196afcca95a5abf44391188c695c6c1456e16154c75a211d238cc3bc5cb47') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); }"
      php composer-setup.php
      php -r "unlink('composer-setup.php');"
      mv composer.phar /home/vagrant/.bin/composer
      chmod a+x /home/vagrant/.bin/composer
      chown -R vagrant:vagrant /home/vagrant/.bin/composer
    fi
    
    # Copy the Nginx configuration and restart the web server
    echo "Copying Nginx configuration"
    sudo service nginx stop
    sudo killall nginx
    sudo sed -i "s/sendfile on/sendfile off/g" /etc/nginx/conf/nginx.conf
    sudo rm -rf /etc/nginx/conf/conf.d/*
    sudo cp /var/www/protected/config/vagrant-nginx.conf /etc/nginx/conf/conf.d/http.conf
    sudo service nginx start

    # Create the database
    echo "Creating MySQL database if it is not present"
    mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS root;"

    # Update Composer
    /home/vagrant/.bin/composer self-update

    sudo chmod -R 774 /var/www/protected/runtime
    sudo chmod -R 774 /var/www/protected/config
    sudo chmod -R 774 /var/www/web/assets

    # Install the website
    cd /var/www
    rm -rf /var/www/vendor
    /home/vagrant/.bin/composer install -ovn

    if [[ ! -f /var/www/protected/config/main.php ]]
    then
      $(which php) web/index.php installer index --dbHost=127.0.0.1 --dbName=root --dbUsername=root --dbPassword=root --adminEmail=root@example.com --adminPassword=root1234 --adminUsername=root --siteName=CiiMS
      echo -n "================================================================"
      echo -n "CiiMS Vagrant box has now been installed"
      echo -n "CiiMS can be accessed by opening your browser to 127.0.0.1:8080"
      echo -n "Your credentials are root@example/root1234"
      echo -n "================================================================"
    fi
  SHELL
end