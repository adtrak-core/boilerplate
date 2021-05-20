# Addon Scripts

Addon scripts are those that are not required for every page on the website. These could include:
- Carousels
- Reviews
- etc.

As they are not needed on all pages of the website, they can be conditionally included using the ```enqueue.php``` functions file.

## Usage

Place your addon scripts into this folder. Gulp will know not to bundle these scripts into production.
Instead, you will get ```production-[scriptname].js``` in your ```/dist``` folder when you run ```npm run dev```.


## Enqueuing your scripts for specific pages
Open ```_functions/enqueue.php```. On line 6 you will find an example ```if``` statement showing how a script can be included on a specific page (page-slug, page-slug-2).

Adjust the ```if``` statements as required to conditionally enqueue your scripts.