
![Image](https://i.imgur.com/9HssB8T.png)
## An open source hacking simulated written in PHP

Syscrack, to put it simply, Is a virtual hacking simulator. By which you control a virtual computer inside a simulated IP based internet. Your tasks vary depending on your play style, making the game seem more like a virtual sandbox.

Installation
===========

Below I'll cover the basic steps to setting up syscrack using the current github source available as of writing.

## Windows

Windows is by far the easiest platform to run Syscrack on, as it was the OS used in development. You'll simply need to head over to https://www.apachefriends.org/xampp-files/7.2.1/xampp-win32-7.2.1-0-VC15-installer.exe and install the following exectable.

Once you have installed xampp, and made sure all of your services are running. Head over to https://getcomposer.org/ and install composer. It will ask for a CLI interpreter, this will be in the location of where ever you installed xampp nested in the PHP folder. You'll want to look for php.exe

Once composer is installed, simply run one of the various build batch scripts provided. It is suggested that for your first build that you run the apache2-dev build for windows. This will automatically configure your webserver ready for Syscrack.

## Linux

I'll post a linux tutorial soon.

Setting Up
===========

Setting up Syscrack is a fairly easy process. Simply head to http://localhost/developer/ and you will see a wide set of tools designed to make the installation process simple.

What you'll need to do first is create a mysql database. Remember the name you give it as you'll need it later for creating your database settings.

You'll need your mysql username and password handy too. Once you have all the information you need, head over to http://localhost/developer/connection/creator/ and enter your mysql username, password, the database you created before, and the host. The host should always be localhost unless you are using an external database server.

Once entered, hit submit and if you see a success message, it worked. If you do not, please check the file permissions of your environment. Please make sure chmod is set to 7777 and syscracks directories are globally readable and writable.

Next, you are going to want to head to http://localhost/developer/migrator/, if you do not see any text in the box to your right. Please check that you have installed syscracks conf files correctly. To populate the database ready for Syscracks game engine, simply hit migrate database. If you see a success message, its all gone good.

Playing Around
===========

Now that you've set up syscrack, when you go to the index you should see the homepage. Register a new account, by default the first account will always be made admin. 

Now, you can explore! I suggest hover overing the icons in the navbar to understand what they do. Click around and try and learn the locations of all the buttons. At the right of the navigational bar next to the account dropdown box is the admin control panel. From here you can edit various game settings, and create computers into the virutal network.

You'll need to create computers in order to have something to hack, right now as of writing this sucks to do and I'm changing it very soon. For now, try and understand the formatting I am using and simply change numbers, look out for syntax errors.

Plans
===========

Syscrack is by no means complete, nor a final product. There's still a lot of changes to occur, and I do appologise if the documentation is a little sucky right now. Please, bare with us!
