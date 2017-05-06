![Syscrack](https://syscrack.net/assets/img/vibrant_green.png)

### AN OPEN SOURCE HACKING SIMULATOR, SIMULATED ON A VIRTUAL INTERNET

Syscrack is a hacking simulator built from the ground up to be expandable and modded. Users are thrown into a virtual internet and are given a set of tools. The aim is simple, become the best hacker in the world.

### Hosting & Development

Syscrack is very easy to host, and extremely straight forward to getting modding right away!

#### Windows

It is suggest that for Windows development that you use XAMPP, as it is what I used to develop Syscrack, here's a list of things you'll need installed on your system.

1. Composer
2. XAMPP ( PHP 7.0 + ) with MySQL
3. Memcache ( if you are using the memory cache services )

To launch and develop Syscrack, it is a simple drag and drop operation into your htdocs folder, or equivilent. Download the latest release, or if you feel, clone the github. Move over the files into your respective apache2 website folder and then run...

```
composer install
```

If all your extensions are currently active, this will not produce any errors. But in the likely hood that they are not, please edit your php.ini settings and enable the plugins that composer is requesting.

You will then need to create a .htaccess file inside your apache2 website folder. Simply copy the code below into your .htaccess file that you have just created.

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

<IfModule mod_mime.c>
 AddType application/x-javascript .js
 AddType text/css .css
</IfModule>
<IfModule mod_deflate.c>
 AddOutputFilterByType DEFLATE text/css application/x-javascript text/x-component text/html text/richtext image/svg+xml text/plain text/xsd text/xsl text/xml image/x-icon application/javascript
 <IfModule mod_setenvif.c>
  BrowserMatch ^Mozilla/4 gzip-only-text/html
  BrowserMatch ^Mozilla/4\.0[678] no-gzip
  BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
 </IfModule>
 <IfModule mod_headers.c>
  Header append Vary User-Agent env=!dont-vary
 </IfModule>
</IfModule>
```

If you are continuing to get an 404 error on all pages, please look if your rewriting is enabled on your current set up. Another reason could be that your .htaccess file isn't being loaded. Check for spelling mistakes, and write some 'garbage' at the top of the file to check if apache2 is accessing the file, it should give you a 503 error if so. 

Once you have installed composer, and set up your .htaccess file, you can fire up localhost and check out if the website is working. You should see a 'Database Error', Syscracks framework automatically encrypts and saves your database settings to a secure file for your convinence. The next step is to head to...

```
http://localhost/developer/
```

Here you will be able to access the developer panel, which makes your life a whole lot easier in regards to setting up the game. Using the connection creator tool, enter your database username, password, host and database. Then, using the connection status tool, check if your database has successfully been able to connect, you will see a 'Success' message, a long with a json file of the settings you are currently using to connect.

After you have successfully connected, the next step is to migrate your database. By default, Syscrack comes loaded with a default schema which is automatically loaded into the database migrator input box, so it should be as simple as pressing 'migrate database'. If not, please check the github for the database schema file and simply paste the json string into the box. You will recieve a success message.

After you have successfully created your connection, checked the status of your database connection and migrated your database. Head to the index page, and check it out! You should see the website functioning as it should be, stylish HTML boostrap and all. If you would like to change any of the settings ( which I suggest you do ), head to..

```
http://localhost/developer/settingsmanager/
```

Here, you will be able to edit many of the settings of the framework, and game. But be careful, some of these settings are very important and must remain the type that they have been set as, else you might embrace errors! If you would like to disable disable registrations for testing purposes, that can be done by editing the setting...

```
http://localhost/developer/settingsmanager/#setting_user_allow_registrations
```

Or for instance, only allow people with beta-keys to sign up...

```
http://localhost/developer/settingsmanager/#setting_user_require_betakey
```

Remember, when you are finished developing you will need to disable this section. By default it is higher than the game and thus doesn't require you to login, or be an admin. This is because the developer section is meant to be a platform for development and not to be used when the website is live. To disable this section, simply head to...

```
http://localhost/developer/disable/
```

Syscrack might complain that some directories are missing in your /syscrack/ folder and will give you an error when you register a new account, if this is the case, please check that these files exist in your roots folder.

```
/syscrack/addressdatabase/
/syscrack/bankdatabase/
/syscrack/logs/
/syscrack/npc/
```

#### Linux

A linux tutorial will be coming soon, as this one is a little more tricky...

## Modding

Modding is extremely simple, the game is built upon a dynamic framework. It is suggested that for now while the wiki is being wrote, that you check out the github and look at how the current softwares, pages and operations are being created. We use FlightPHP for our routing, so if you want to learn more about how to do URL magic, please look up the their documentation.

