# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "debian/jessie64"
  config.vm.network "private_network", ip: "192.168.30.10"
  config.vm.synced_folder ".", "/vagrant", id: "v-root", mount_options: ["rw", "tcp", "nolock", "noacl", "async"], type: "nfs", nfs_udp: false
  config.vm.provision "shell", inline: <<-SHELL
    # Force to move to /vagrant on login
    echo "cd /vagrant" >> /home/vagrant/.bashrc
    echo 'deb http://packages.dotdeb.org jessie all' | sudo tee /etc/apt/sources.list.d/dotdeb.list
    wget -O- https://www.dotdeb.org/dotdeb.gpg | sudo apt-key add -

    sudo apt-get update
    sudo apt-get install -y curl git php7.0-cli php7.0-curl php7.0-intl php7.0-xdebug vim
    cat >/etc/php/mods-available/custom.ini <<EOF
date.timezone = 'UTC'
error_reporting = E_ALL
display_errors = On
display_startup_errors = On
phar.readonly = Off
EOF
    sudo ln -s /etc/php/mods-available/custom.ini /etc/php/7.0/cli/conf.d/99-custom.ini

    php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
    sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
  SHELL
end
