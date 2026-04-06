'use strict';

const gulp = require('gulp');
const browserSync = require('browser-sync').create();
const uglify = require('gulp-uglify');
const cssmin = require('gulp-clean-css');
const sass = require('gulp-dart-sass');
const autoprefixer = require('gulp-autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const rigger = require('gulp-rigger');
const rename = require('gulp-rename');
const del = require('del');
const svgstore = require('gulp-svgstore');
const cheerio = require('gulp-cheerio');
const posthtml = require("gulp-posthtml");
const include = require("posthtml-include");
const jshint = require('gulp-jshint');
const postcss = require('gulp-postcss');
const syntax_scss = require('postcss-scss');
const reporter = require('postcss-reporter');
const stylelint = require('stylelint');

const path = {
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

// CLEAN
function clean() {
  return del(path.clean);
}

// HTML
function html() {
  return gulp.src(path.src.html)
    .pipe(rigger())
    .pipe(posthtml([include()]))
    .pipe(gulp.dest(path.build.html))
    .pipe(browserSync.stream());
}

// STYLES
function styles() {
  return gulp.src(path.src.styles)
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer({ cascade: false }))
    .pipe(cssmin())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(path.build.css))
    .pipe(browserSync.stream());
}

// SCRIPTS
function scripts() {
  return gulp.src(path.src.scripts)
    .pipe(sourcemaps.init())
    .pipe(rigger())
    // .pipe(uglify())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(path.build.js))
    .pipe(browserSync.stream());
}

// IMAGES
function images() {
  return gulp.src(path.src.img)
    .pipe(gulp.dest(path.build.img))
    .pipe(browserSync.stream());
}

// FONTS
function fonts() {
  return gulp.src(path.src.fonts)
    .pipe(gulp.dest(path.build.fonts))
    .pipe(browserSync.stream());
}

// LIBS
function libs() {
  return gulp.src(path.src.libs)
    .pipe(gulp.dest(path.build.libs))
    .pipe(browserSync.stream());
}

// SVG SPRITE
function sprite() {
  return gulp.src('src/img/icon-*.svg')
    .pipe(cheerio({
      run($) {
        $('[fill]').removeAttr('fill');
        $('[stroke]').removeAttr('stroke');
      },
      parserOptions: { xmlMode: true }
    }))
    .pipe(svgstore({ inlineSvg: true }))
    .pipe(rename('sprite.svg'))
    .pipe(gulp.dest(path.build.img))
    .pipe(browserSync.stream());
}

// SERVER
function serve() {
  browserSync.init({
    server: {
      baseDir: path.build.html,
      index: 'index.html'
    }
  });
}

// WATCH
function watcher() {
  gulp.watch(path.watch.html, html);
  gulp.watch(path.watch.styles, styles);
  gulp.watch(path.watch.scripts, scripts);
  gulp.watch('src/img/*.svg', sprite);
}

// LINT JS
function lintJS() {
  return gulp.src(path.src.scripts)
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'));
}

// LINT SCSS
function lintSCSS() {
  const stylelintConfig = {
    rules: {
      "no-empty-source": true,
      "number-leading-zero": "always",
      "property-case": "lower"
    }
  };

  const processors = [
    stylelint(stylelintConfig),
    reporter({ clearMessages: true, throwError: false })
  ];

  return gulp.src(['src/styles/**/*.scss'])
    .pipe(postcss(processors, { syntax: syntax_scss }));
}

// BUILD
const build = gulp.series(
  clean,
  sprite,
  html,
  styles,
  images,
  scripts,
  fonts,
  libs
);

// DEFAULT
exports.default = gulp.series(
  build,
  gulp.parallel(serve, watcher)
);
