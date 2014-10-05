README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or 
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "/var/www/elohim/public"
   ServerName elohim.local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development
    
   <Directory "/var/www/elohim/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>
    
</VirtualHost>


INSTALL

DROITS
public/users => drwxr-xr-x 3 www-data www-data   4096 2011-12-23 02:13 users
supprimer le dossier index
changer le nom du dossier des captchas (public => www) dans le formulaire UserRegister.php)
