# WD Boilerplate

## Prerequisites ##
1. You need to have ```composer``` installed on your machine [https://getcomposer.org/download/](https://getcomposer.org/download/)
2. You need to have ```NodeJS``` installed on your machine [https://nodejs.org/en/download/](https://nodejs.org/en/download/)
3. You need to have ```NPM``` installed on your machine [https://www.npmjs.com/get-npm](https://www.npmjs.com/get-npm)
4. You will need a local working environment (WAMP, MAMP, etc).

### You're now ready to begin ###

## Adtrak Child Tailwind Theme with Twig & Timber (Preferred)

1. Create a new repository on gitlab and clone it to your local machine (```git clone [REPO URL] [FOLDER NAME]```)
2. Download latest release of WordPress [https://wordpress.org/latest.zip](https://wordpress.org/latest.zip)2. 
3. Extract Wordpress to your new folder
4. Delete the ```wp-content``` folder from your new folder
5. Download this boilerplate. Extract it to the folder you create in Step 2.
6. Change theme folder name and update theme details in style.css
7. Rename ```example.gitignore``` to ```.gitignore``` and open the file
8. Edit lines ```116``` & ```117``` and replace the theme name to prevent ```node_modules``` and ```vendor``` files being committed 
9. Create local database
10. Open the Command Line / Terminal 
11. Change Directory to the theme folder (```cd /[FOLDER NAME]/wp-content/themes/[YOUR NEW THEME NAME]```)
12. Run ```npm install```
13. Run ```composer install```
14. From the theme folder, open ```gulpfile.js```
15. Edit line ```89``` to the name of your local site. (e.g. my-new-site.vm)
16. Save the ```gulpfile```
17. Visit your new site in the browser and set up Wordpress (the wp-config file will be ignored by GIT)
18. Activate the theme through the WordPress admin console
19. Open the Command Line / Terminal 
20. Run ```npm run dev``` or ```gulp```
21. ```npm run dev``` will run the ```development``` tasks, and won't minify your SCSS or Javascript

#### The theme structure has changed for this boilerplate. ####

1. All components (```header```, ```footer```, ```phone-top-right``` etc) can be found in ```_components``` 
2. All functions (```script enqueuing```, ```Custom Post Types```, ```Custom Taxonomies``` etc) can be found in ```_functions```
3. All page templates (```front-page.twig```, ```page.twig```, etc) can be found in ```_views```

## Adtrak Child Tailwind Theme (Without Twig & Timber)

To get the Tailwind theme set up:

1. Activate the theme through the WordPress admin console
2. Go to ```gulpfile.js``` and make sure the ```serve``` task is serving the correct local URL by changing the ```proxy``` variable.
3. Open Terminal / Hyper / CMD
4. Navigate to the ```adtrak-child-tailwind``` theme
5. Run ```npm install``` 
6. Once ```npm install``` has finished installing your dependencies, run ```npm run dev``` or ```gulp```
7. ```npm run dev``` will run the ```development``` tasks, and won't minify your SCSS or Javascript

## TailwindCSS

We have created a Tailwind Config file that is easily editable in ```tailwind.config.js```. If you need to add colours, fonts etc, they can be added or edited in this file.

You can access the primary, secondary & tertiary colours by using classes as follows:


| Default   | Lighter           | Darker           |
|-----------|-------------------|------------------|
| primary   | primary-lighter   | primary-darker   |
| secondary | secondary-lighter | secondary-darker |
| tertiary  | tertiary-lighter  | tertiary-darker  |


**Feel free to add your own extensions**

### Tailwind Defaults

The default Tailwind config can be found in ```tailwind.config-default.js```. This is included by default, and is purely here for reference.

For more help with Tailwind, don't forget the [docs](https://tailwindcss.com/docs/installation/)

## Before Deployment
Before you deploy a project, you need to build the ```production``` assets. 
To do this you need to run a different ```gulp``` command. You can either do this locally, or through DeployHQ.

### Building production assets locally

1. In Terminal / Hyper / CMD, navigate to your theme directory
2. Run ```npm run build``` (you can also use ```gulp --production``` if you wish)
3. Check main.min.css is minified before deployment

### Building production assets on DeployHQ
1. Open deployHQ and go to your project
2. Go to 'Build Pipeline' from the left hand menu
3. Click 'NPM' from the Template options
4. In the Command textarea, enter the content below:
```
cd wp-content/themes/adtrak-child-tailwind
npm install --save --quiet
npm run build
```
**Remember to change the command if you have changed the theme name!**



