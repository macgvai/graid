'use strict';

var gulp         = require('gulp'),
  browserSync  = require('browser-sync'),
  reload       = browserSync.reload,
  uglify       = require('gulp-uglify'),
  cssmin       = require('gulp-clean-css'),
  sass         = require('gulp-sass'),
  autoprefixer = require('gulp-autoprefixer'),
  sourcemaps   = require('gulp-sourcemaps'),
  rigger       = require('gulp-rigger'),
  run          = require('run-sequence'),
  rename       = require('gulp-rename'),
  del          = require('del'),
  svgstore     = require('gulp-svgstore'),
  cheerio      = require('gulp-cheerio'),
  posthtml     = require("gulp-posthtml"),
  include      = require("posthtml-include");

var path = {
  build: {
    html:    'build/',
    js:      'build/js/',
    css:     'build/css/',
    img:     'build/img/',
    libs:    'build/libs/',
    fonts:   'build/fonts/'
  },
  src: {
    html:    'src/*.html',
    scripts: 'src/scripts/*.js',
    styles:  'src/styles/*.scss',
    img:     'src/img/**/*.*',
    libs:    'src/libs/**/*.*',
    fonts:   'src/fonts/**/*.*'
  },
  watch: {
    html:    'src/**/*.html',
    scripts: 'src/scripts/**/*.js',
    styles:  'src/styles/**/*.scss',
    img:     'src/img/**/*.*',
    fonts:   'src/fonts/**/*.*'
  },
  clean: 'build'
};

gulp.task('clean:build', function () {
  return del(path.clean);
});

gulp.task('html:build', function() {
  gulp.src(path.src.html)
    .pipe(rigger())
    .pipe(posthtml([
      include()
    ]))
    .pipe(gulp.dest(path.build.html))
    .pipe(browserSync.stream());
});

gulp.task('styles:build', function() {
  gulp.src(path.src.styles)
    .pipe(sourcemaps.init())
    .pipe(sass({
      errLogToConsole: true
    }).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 2 versions']
      , cascade: false
      , remove: true
    }))
    .pipe(cssmin({ restructuring: false, semanticMerging: false }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest(path.build.css))
    .pipe(browserSync.stream());
});

gulp.task('image:build', function() {
  gulp.src(path.src.img)
    .pipe(gulp.dest(path.build.img));
});

gulp.task('sprite:build', function () {
  return gulp.src('src/img/icon-*.svg')
    .pipe(cheerio({
      run ($) {
        $('[fill]').removeAttr('fill');
        $('[stroke]').removeAttr('stroke');
      },
      parserOptions: {xmlMode: true}
    }))
    .pipe(svgstore({
      inlineSvg: true
    }))
    .pipe(rename('sprite.svg'))
    .pipe(gulp.dest(path.build.img))
    .pipe(browserSync.stream());
});

gulp.task('scripts:build', function() {
  gulp.src(path.src.scripts)
    .pipe(sourcemaps.init())
    .pipe(rigger())
    // .pipe(uglify().on('error', function(e){console.log(e.message)}))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(path.build.js))
    .pipe(browserSync.stream());
});

gulp.task('fonts:build', function() {
  gulp.src(path.src.fonts)
    .pipe(gulp.dest(path.build.fonts))
    .pipe(browserSync.stream());
});

gulp.task('libs:build', function() {
  gulp.src(path.src.libs)
    .pipe(gulp.dest(path.build.libs))
    .pipe(browserSync.stream());
});

// Sync
gulp.task('init-sync', ['build'], function () {
  return browserSync({
    server: {
      baseDir: path.build.html,
      index: 'index.html'
    }
  });
});

gulp.task('build', function (done) {
  run(
    'clean:build',
    'sprite:build',
    'html:build',
    'styles:build',
    'image:build',
    'scripts:build',
    'fonts:build',
    'libs:build',
    done
  )
});

gulp.task('watch', ['build'], function() {
  gulp.watch([path.watch.html], function(event, cb) {
    gulp.start('html:build');
  });
  gulp.watch([path.watch.styles], function(event, cb) {
    gulp.start('styles:build');
  });
  gulp.watch([path.watch.scripts], function(event, cb) {
    gulp.start('scripts:build');
  });
  gulp.watch(['src/img/*.svg'], function(event, cb) {
    gulp.start('sprite:build');
  });
});


gulp.task('default', ['build', 'watch', 'init-sync']);

/*********************************************/

var jshint = require('gulp-jshint');

gulp.task('lint-js', function() {
  return gulp.src(path.src.scripts)
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'));
});

/*********************************************/

var postcss     = require('gulp-postcss'),
  syntax_scss = require('postcss-scss'),
  reporter    = require('postcss-reporter'),
  stylelint   = require('stylelint');

gulp.task("lint-scss", function() {

  // Stylelint config rules
  var stylelintConfig = {
    "rules": {
      "media-feature-range-operator-space-before": "always",
      "media-query-list-comma-newline-after": "always",
      "media-query-list-comma-newline-before": "always",
      "media-query-list-comma-space-after": "always",
      "media-query-list-comma-space-before": "always",
      "no-descending-specificity": true,
      "no-duplicate-at-import-rules": true,
      "no-duplicate-selectors": true,
      "no-empty-source": true,
      "no-eol-whitespace": true,
      "no-extra-semicolons": true,
      "no-invalid-double-slash-comments": true,
      "no-missing-end-of-source-newline": true,
      "no-unknown-animations": true,
      "number-leading-zero": "always",
      "number-no-trailing-zeros": true,
      "property-case": "lower"
    }
  }

  var processors = [
    stylelint(stylelintConfig),
    reporter({
      clearMessages: true,
      throwError: false
    })
  ];

  return gulp.src(
    ['src/styles/**/*.scss',
      // Ignore linting vendor assets
      // Useful if you have bower components
      '!src/styles/main.scss']
  )
    .pipe(postcss(processors, {syntax: syntax_scss}));
});
