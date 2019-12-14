# [OregonFlora](https://symbiota.oregonflora.org)

A [Symbiota](http://symbiota.org) portal focusing on the vascular plants of Oregon. For the Symbiota README, see 
[here](https://github.com/Symbiota/Symbiota/blob/master/docs/README.txt).

The site features content that diverges significantly from the Symbiota project, while still adhering to the 
Symbiota database structure and maintaining core Symbiota features.
For the site content that differs from Symbiota, which includes the home page, garden page, and taxonomic profiles,
the following OregonFlora diverges in the following ways: 
   - [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html) is used to access the database, providing 
     an asynchronous JSON API. This is done mainly to decouple the PHP-based server-side code from the front end.
     [Composer](https://getcomposer.org/) is used as the build system.
   - For the front end, [ReactJS](https://reactjs.org) and [Less](http://lesscss.org/) are used. 
   [NodeJS](https://nodejs.org/) is used as the build system.

### To build the front end:
1. Install the NodeJS dependences: `npm install`
2. Build the React- and Less-based pages: `npm run build`

For a development server that watches for changes in .js/.jsx/.less files and automatically rebuilds:

`npm run devstart`

### To build the back end:
Install the PHP dependencies: `composer install`
