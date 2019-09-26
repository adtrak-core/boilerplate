# WD Boilerplate

1. Clone/Download this repo. Extract it somewhere.
2. Download latest release of WordPress [https://wordpress.org/latest.zip](https://wordpress.org/latest.zip)
3. Extract it to a folder.
4. Delete the wp-content and wp-config-sample from the downloaded file.
5. Copy the contents into the repo folder you downloaded earlier.
6. Edit the wp-config.php with new salt keys/live site details.
7. Edit the wp-config-local.php with your local database settings.
8. Edit the wp-config-staging.php with your staging database settings.
9. Go to town.

## Adtrak Child Tailwind Theme

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



