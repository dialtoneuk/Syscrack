![Image](https://i.imgur.com/9HssB8T.png)
## An open source hacking simulated written in PHP

Syscrack, to put it simply, Is a virtual hacking simulator. By which you control a virtual computer inside a simulated IP based internet. Your tasks vary depending on your play style, making the game seem more like a virtual sandbox.

Installation
===========

Below I'll cover the basic steps to setting up Syscrack using the current GitHub source available as of writing.

## Windows

Windows is by far the easiest platform to run Syscrack on, as it was the OS used in development. You'll simply need to head over to https://www.apachefriends.org/xampp-files/7.2.1/xampp-win32-7.2.1-0-VC15-installer.exe and install the following executable.

Once you have installed xampp, and made sure all of your services are running. Head over to https://getcomposer.org/ and install composer. It will ask for a CLI interpreter, this will be in the location of where ever you installed xampp nested in the PHP folder. You'll want to look for php.exe

Once composer is installed, simply run one of the various build batch scripts provided. It is suggested that for your first build that you run the apache2-dev build for windows. This will automatically configure your webserver ready for Syscrack.

### Starting Fresh On Windows

If somehow you have screwed up your settings file. You can simply run the reset-win-settings batch file provided.

## Ubuntu

Ubuntu requires a couple of extra steps compared to the Windows version. I highly suggest if you are simply testing Syscrack to run it instead using Windows. As the installation process is far more simple and straight forward.

For the basis of this tutorial, I'll be using nginx. So your first step is to install the latest version of nginx. Make sure that you have ran updates on your box before you begin installing the required packages. Below is a helpful tutorial provided by digital ocean with step by step instructions on how to configure nginx with the correct version of PHP.

https://www.digitalocean.com/community/tutorials/how-to-upgrade-to-php-7-on-ubuntu-14-04

Afterwards, you are going to want to install Composer. Composer will give an error if you do not have the php-mbstring packages installed, as well as the php-cli package. Please be sure to check the terminal output for extension errors. It is a common mistake to suddenly go into the php.ini and edit these settings. Make sure first that you have these modules installed, and then check that you have them enabled in the settings.

After composer is installed, you are going to want to open a fresh FTP session with an FTP client. I suggest using FileZilla. Navigate to the folder var/www/html and drag and drop your syscrack installation files into this folder.

You are then going to want to go to your terminal, and set your current active directory to var/www/html and then run Composer Install. If you did the last two steps correctly, you should get no errors and composer should download the required dependencies. 

Afterwards, you are going to want to navigate to your nginx web config file and add into your config line the index rewrite required for Syscracks page builder to function. Simply add all the text below to your config file, if you already have the location set, simply add the lines inside the method into the respective method in your config file.

```
## Please palce everything below into your nginx.config file

server {
    location / {
        try_files $uri $uri/ /index.php;
    }
}

##Please look if gzip already exists as a line and instead change it to on instead of copy and pasting the same setting twice!
gzip on;

##
gzip_vary on;
gzip_min_length 10240;
gzip_proxied expired no-cache no-store private auth;
gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml;
gzip_disable "MSIE [1-6]\.";
```

Once done, give nginx a restart and then go to your servers address. You should see an error! This is because you haven't set up your database yet, check out the tutorial below on how to do that and get Syscrack configurated!

### Permission Errors

Depending on your config, you could have permission errors when Syscracks attempts to read and write data. This is mostly common on linux machines but can sometimes be present on Windows machines too.

To fix, all you need to do is simply set the permissions of the following folders to the respective values using the chmod command. You can look up how to set directy permissions via the link below.

https://askubuntu.com/questions/303593/how-can-i-chmod-777-all-subfolders-of-var-www

Setting Up
===========

Setting up Syscrack is a fairly easy process. Simply head to http://localhost/developer/ and you will see a wide set of tools designed to make the installation process simple.

What you'll need to do first is create a MySQL database. Remember the name you give it as you'll need it later for creating your database settings.

You'll need your MySQL username and password handy too. Once you have all the information you need, head over to http://localhost/developer/connection/creator/ and enter your MySQL username, password, the database you created before, and the host. The host should always be localhost unless you are using an external database server.

Once entered, hit submit and if you see a success message, it worked. If you do not, please check the file permissions of your environment. Please make sure chmod is set to 7777 and Syscracks directories are globally readable and writable.

Next, you are going to want to head to http://localhost/developer/migrator/, if you do not see any text in the box to your right. Please check that you have installed Syscracks config files correctly. To populate the database ready for Syscracks game engine, simply hit migrate database. If you see a success message, its all gone good.

Playing Around
===========

Now that you've set up Syscrack, when you go to the index you should see the homepage. Register a new account, by default the first account will always be made admin. 

Now, you can explore! I suggest hovering over the icons in the navbar to understand what they do. Click around and try and learn the locations of all the buttons. At the right of the navigational bar next to the account dropdown box is the admin control panel. From here you can edit various game settings, and create computers into the virtual network.

You'll need to create computers in order to have something to hack, right now as of writing this sucks to do and I'm changing it very soon. For now, try and understand the formatting I am using and simply change numbers, look out for syntax errors


Plans
===========

Syscrack is by no means complete, nor a final product. There's still a lot of changes to occur, and I do apologise if the documentation is a little sucky right now. Please, bare with us!
