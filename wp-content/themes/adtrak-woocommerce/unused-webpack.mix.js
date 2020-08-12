let mix = require("laravel-mix");

mix
	.js("src/js/app.js", "assets/js")
	.postCss("src/css/app.css", "assets/css/", [
		require("postcss-import"),
		require("postcss-nested"),
		require("tailwindcss"),
		require("autoprefixer")
	]);
