# Readme

- Put your WP Parts within parts. Declare using `get_template_part('parts/part-name.php')`. In some situations you may need to use `include locate_template('parts/part-name')` if you need to pass PHP in and out of it. Self contained php use `get_template_part`
- Put all your files in the predefined folders.
- Use `get_theme_file_uri('images/logo.svg')` when referencing files withing (WordPress 4.7+).
- Use `get_parent_theme_file_uri('css/style.scss')` if you need to reference anything in the parent theme (WordPress 4.7+).