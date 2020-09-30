/**************************
 * Gulpfile Dependencies
**************************/

let gulp            = require('gulp'),
    browserSync     = require('browser-sync').create(), // Requires the browser-sync plugin
    autoprefixer    = require('autoprefixer'),
    cssnano         = require('cssnano'),
    argv            = require('yargs').argv,
    sass            = require('gulp-sass'), // Requires the gulp-sass plugin
    uglify          = require('gulp-uglify'),    
    gulpIf          = require('gulp-if'),    
    rename          = require("gulp-rename"),    
    postcss         = require('gulp-postcss'),
    purgecss        = require('gulp-purgecss'),    
    concat          = require('gulp-concat');


class TailwindExtractor {
  static extract(content) {
    return content.match(/[A-z0-9-:\/]+/g);
  }
}

/**************************
 * Task Styles
**************************/
gulp.task('styles', function () {
  return gulp.src('*/**.scss')
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
      content: ['**/*.php'],
      whitelist: ['buckets--num-4', 'sub-menu', 'mob-nav--active', 'mob-nav-underlay', 'sub-arrow', 'mob-nav-close', 'ninja-forms-field', 'active'],
      extractors: [
        {
          extractor: TailwindExtractor,
          extensions: ["php", ".js"]
        }
      ]
    })
  ))
  .pipe(rename('main.min.css'))
  .pipe(gulp.dest('css/'))
  .pipe(browserSync.reload({
    stream: true
  }))
})

/**************************
 * Task Scripts
**************************/
gulp.task('scripts', function() {
  return gulp.src('js/scripts/*.js')
    .pipe(concat('production-dist.js'))
    .pipe(gulpIf(argv.production, uglify()))
    .pipe(gulp.dest('js/'))
    .pipe(browserSync.reload({
     stream: true
  }))
});


/**************************
 * Task Watch
**************************/
gulp.task('watch', () => {
  gulp.watch(`styles/**/*.scss`, gulp.series('styles'));
  gulp.watch(`js/scripts/*.js`, gulp.series('scripts'));
});


/**************************
 * Task Serve
**************************/
gulp.task('serve', () => {
    browserSync.init({
    proxy: `adtrak-boilerplate.vm`,
    files: ['**/*.php', '**/*.js', '**/*.twig', '**/*.scss'],
    ghostMode : false
  })
})


/**************************
 * Gulp Automation
**************************/
gulp.task('default', gulp.parallel('styles', 'scripts', 'watch', 'serve'));
gulp.task('build', gulp.parallel('styles', 'scripts'));
