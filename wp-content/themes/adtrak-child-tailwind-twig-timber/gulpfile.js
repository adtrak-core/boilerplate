/**************************
 * Gulpfile Dependencies
**************************/

let gulp            = require('gulp'),
    browserSync     = require('browser-sync').create(), // Requires the browser-sync plugin
    autoprefixer    = require('autoprefixer'),
    cssnano         = require('cssnano'),
    argv            = require('yargs').argv,
    sass            = require('gulp-sass'), // Requires the gulp-sass plugin
    terser          = require('gulp-terser'), // Uglify, but does ES6   
    gulpIf          = require('gulp-if'),    
    rename          = require("gulp-rename"),    
    postcss         = require('gulp-postcss'),
    purgecss        = require('gulp-purgecss'),    
    source          = require('vinyl-source-stream'),
    buffer          = require('vinyl-buffer'),
    rollup          = require('@rollup/stream'),

    // Allows for multiple input files for Rollup
    multi           = require('@rollup/plugin-multi-entry'),
    
    // Add support for require() syntax
    commonjs        = require('@rollup/plugin-commonjs'),

    // Add support for importing from node_modules folder like import x from 'module-name'
    nodeResolve     = require('@rollup/plugin-node-resolve');


class TailwindExtractor {
  static extract(content) {
    return content.match(/[A-z0-9-:\/]+/g);
  }
}

/**************************
 * Task Styles
**************************/
gulp.task('styles', function () {
  return gulp.src('_resources/*/**.scss')
  .pipe(sass())
  .pipe(gulpIf(argv.production, 
    postcss([
      require('tailwindcss'),
      autoprefixer(),
      cssnano()
    ]),
    postcss([
        require('tailwindcss')
    ])
  ))
  .pipe(gulpIf(
    argv.production,
    purgecss({
      content: ['**/*.twig'],
      whitelistPatterns: [/nf/, /wp/],
      whitelist: ['buckets--num-4','textarea','ld-area','ld-location','closed', 'open', 'sub-menu', 'mob-nav--active', 'mob-nav-underlay', 'sub-arrow', 'mob-nav-close', 'ninja-forms-field', 'active'],
      extractors: [
        {
          extractor: TailwindExtractor,
          extensions: [".twig", ".js"]
        }
      ]
    })
  ))
  .pipe(rename('main.min.css'))
  .pipe(gulp.dest('dist/'))
  .pipe(browserSync.reload({
    stream: true
  }))
})


/**************************
 * Scripts using rollup.js
 * https://stackoverflow.com/questions/47632435/es6-import-module-with-gulp/59786169#59786169
**************************/

var cache;

gulp.task('scripts', function() {
  return rollup({
      // Point to the entry folder for all JS files
      input: '_resources/**/*.js',
      // Apply plugins
      plugins: [commonjs(), nodeResolve(), multi()],
      // Use cache for better performance
      cache: cache,
      // Output bundle is intended for use in browsers
      format: 'iife',
    })
    .on('bundle', function(bundle) {
      // Update cache data after every bundle is created
      cache = bundle;
    })
    // Name of the output file.
    .pipe(source('production-dist.js'))
    .pipe(buffer())
    .pipe(gulpIf(argv.production, terser()))
    .pipe(gulp.dest('dist/'))
    .pipe(browserSync.reload({
      stream: true
    }))
});

/**************************
 * Task Watch
**************************/
gulp.task('watch', () => {
  gulp.watch(`_resources/styles/**/*.scss`, gulp.series('styles'));
  gulp.watch(`_resources/js/**/*.js`, gulp.series('scripts'));
});


/**************************
 * Task Serve
**************************/
gulp.task('serve', () => {
    browserSync.init({
    proxy: `adtrak-boilerplate.vm`,
    files: ['**/*.php', '**/*.js', '**/*.twig', '**/*.scss'],
    ghostMode : false,
    open: false
  })
})


/**************************
 * Gulp Automation
**************************/
gulp.task('default', gulp.parallel('styles', 'scripts', 'watch', 'serve'));
gulp.task('build', gulp.parallel('styles', 'scripts'));
