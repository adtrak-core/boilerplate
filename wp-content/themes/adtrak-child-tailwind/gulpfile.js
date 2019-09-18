/*global process require*/

/*
 * ----------------------
 * Gulpfile
 * ----------------------
 */


/*
 * Dependencies
 */

let gulp            = require('gulp'),
    sass            = require('gulp-sass'), // Requires the gulp-sass plugin
    browserSync     = require('browser-sync').create(), // Requires the browser-sync plugin
    uglify          = require('gulp-uglify'),
    gulpIf          = require('gulp-if'),
    cssnano         = require('gulp-cssnano'),
    rename          = require("gulp-rename"),
    imagemin        = require('gulp-imagemin'),
    autoprefixer    = require('gulp-autoprefixer'),
    postcss         = require('gulp-postcss'),
    purgecss         = require('gulp-purgecss'),
    concat          = require('gulp-concat');

/*
 * Task - Serve
 */

gulp.task('serve', () => {
     browserSync.init({
     proxy: `boilerplate.vm`,
     files: `**/*`,
     ghostMode : false
  })
})


/*
 * Task - Sass
 */
gulp.task('styles', function () {
  return gulp.src('styles/**/*.scss')
  .pipe(postcss([
    require('tailwindcss'),
    require('autoprefixer'),
  ]))
  .pipe(sass())
  // .pipe(purgecss({
  //     content: ['*.php']
  // }))
  .pipe(cssnano())
  .pipe(rename('main.min.css'))
  .pipe(gulp.dest('css/'))
  .pipe(browserSync.reload({
    stream: true
  }))
})

/*
* Task - Scripts
*/
gulp.task('scripts', function() {
  return gulp.src('js/scripts/*.js')
    .pipe(gulpIf('*.js', uglify()))
    .pipe(concat('production-dist.js'))
    .pipe(gulp.dest('js/'))
    .pipe(browserSync.reload({
     stream: true
  }))
});

/*
 * Task - Watch
 */
gulp.task('watch', () => {
    gulp.watch(`scss/**/*.scss`, gulp.series('styles'));
    gulp.watch(`js/scripts/run.js`, gulp.series('scripts'));
});


/*
 * Gulp automation tasks
 */

gulp.task('default', gulp.parallel('styles', 'scripts', 'watch', 'serve'));
gulp.task('build', gulp.parallel('styles', 'scripts'));
