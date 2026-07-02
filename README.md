# Things to install

* Lando ( https://docs.lando.dev/basics/installation.html )
* Node ( https://nodejs.org/en/download/ )
* Gulp ( https://gulpjs.com/docs/en/getting-started/quick-start/ )

# Getting your local up and running

Open a terminal and CD to Repo directory

copy and rename `wp-config-lando.php` to `wp-config.php`  
copy and rename `.htaccess.local` to `.htaccess`  

`lando start`  
`lando wp core download --skip-content` ( if you are installing for the first time )

`64XHPyuPfZPF8qRExW`

This will install/build Lando at https://tiffanyotten.lndo.site/ as well as adminer ( a phpmyadmin clone ) at http://adminer.tiffanyotten.lndo.site/

Navigate to the tiffanyotten theme directory, install dependencies and start vite

`npm`
`npm dev`
