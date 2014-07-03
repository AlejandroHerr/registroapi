var p = require('./package.json')
var config = require('./gulp.config.json')
var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var cssmin = require('gulp-cssmin');
var rename = require('gulp-rename');
var ngmin = require('gulp-ngmin');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var html2js = require('gulp-html2js');
var htmlmin = require('gulp-htmlmin');
var clean = require('gulp-clean');
var inject = require('gulp-inject');
var watch = require('gulp-watch');
var replace = require('gulp-replace');
var ftp = require('gulp-ftp');
var prettify = require('gulp-jsbeautifier');

gulp.task('build-clean', function () {
  return gulp.src([config.build_assets_dir + '/*.js', config.build_assets_dir + '/*.css'], {
      read: false,
      force: true
    })
    .pipe(clean());
});
gulp.task('build-styles', function () {
  return gulp.src(config.src_dir + '/less/main.less')
    .pipe(less())
    .pipe(rename({
      basename: p.name,
      suffix: '-' + p.version
    }))
    .pipe(gulp.dest(config.build_assets_dir))
});
gulp.task('build-templates', function () {
  return gulp.src(config.app.templates)
    .pipe(htmlmin({
      collapseWhitespace: true
    }))
    .pipe(html2js({
      base: 'src_frontend/app',
      outputModuleName: 'templates-app',
      useStrict: true
    }))
    .pipe(concat('templates.js'))
    .pipe(gulp.dest(config.build_assets_dir + '/delete'));
});
gulp.task('build-js', ['build-templates'], function () {
  return gulp.src(config.vendor.js.concat(config.app.js, [config.build_assets_dir + "/delete/templates.js"]))
    .pipe(concat(p.name + '-' + p.version + '.js'))
    .pipe(replace('remoteBackendURI', config.localBackend))
    .pipe(gulp.dest(config.build_assets_dir));
});
gulp.task('build-assets', function () {
  return gulp.src(config.vendor.assets.concat(config.app.assets))
    .pipe(gulp.dest(config.build_assets_dir))
});
gulp.task('build-watch', function () {
  gulp.src(config.src_dir + '/less/*.less')
    .pipe(watch(function () {
      gulp.start('build-styles');
    }));
  gulp.src(config.app.templates.concat(config.app.js))
    .pipe(watch(function () {
      gulp.start('build-js');
    }));
});
gulp.task('build', ['build-clean', 'build-js', 'build-styles', 'build-assets'], function () {
  gulp.src([config.src_dir + '/index.html'])
    .pipe(inject(gulp.src([config.build_assets_dir + "/*.js", config.build_assets_dir + "/*.css"], {
      read: false
    }), {
      ignorePath: 'build/',
      addRootSlash: true
    }))
    .pipe(gulp.dest(config.build_dir))
  gulp.src([config.build_assets_dir + '/delete'], {
    read: false,
    force: true
  })
    .pipe(clean());
  gulp.start('build-watch');
});
/***DEPLOY***/
gulp.task('clean-deploy-css', function () {
  return gulp.src([config.deploy_assets_dir + '/*.css'], {
      read: false,
      force: true
    })
    .pipe(clean());
});
gulp.task('clean-deploy-js', function () {
  return gulp.src([config.deploy_assets_dir + '/*.js'], {
      read: false,
      force: true
    })
    .pipe(clean());
});
gulp.task('clean-deploy', ['clean-deploy-js', 'clean-deploy-css'], function () {});
gulp.task('css-deploy', function () {
  return gulp.src(config.build_assets_dir + '/' + p.name + '-' + p.version + '.css')
    .pipe(cssmin())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.deploy_assets_dir));
});
gulp.task('js-deploy', function () {
  return gulp.src(config.build_assets_dir + '/' + p.name + '-' + p.version + '.js')
    .pipe(ngmin())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(uglify({
      mangle: false
    }))
    .pipe(gulp.dest(config.deploy_assets_dir));
});
gulp.task('deploy-app', ['clean-deploy', 'js-deploy', 'css-deploy'], function () {})
gulp.task('deploy-index', [], function () {
  return gulp.src([config.src_dir + '/index.html'])
    .pipe(inject(gulp.src([config.deploy_assets_dir + "/*.js", config.deploy_assets_dir + "/*.css"], {
      read: false
    }), {
      ignorePath: 'app/',
      addRootSlash: false
    }))
    .pipe(gulp.dest(config.deploy_dir));
});
gulp.task('deploy-assets', function () {
  return gulp.src(config.vendor.assets.concat(config.app.assets))
    .pipe(gulp.dest(config.deploy_assets_dir))
});
gulp.task('post-deploy', ['deploy-index', 'deploy-assets'], function () {});
gulp.task('deploy', ['deploy-app'], function () {
  return gulp.start('post-deploy');
});
/***DEFAULT***/
gulp.task('default', ['build'], function () {
  return gulp.start('deploy');
});
gulp.task('clean-all', function () {
  return gulp.src([config.build_dir, config.deploy_dir], {
      read: false,
      force: true
    })
    .pipe(clean());
});
gulp.task('upload', function () {
  return gulp.src(config.deploy_dir + '/**')
    .pipe(replace(config.localBackend, config.remoteBackend))
    .pipe(ftp(config.ftp));
});
gulp.task('prettify', function () {
  gulp.src(config.app.js.concat(['./gulpfile.js']), {
    base: './'
  })
    .pipe(prettify({
      js: {
        braceStyle: "collapse",
        breakChainedMethods: true,
        e4x: false,
        evalCode: false,
        indentChar: " ",
        indentLevel: 0,
        indentSize: 4,
        indentWithTabs: false,
        jslintHappy: false,
        keepArrayIndentation: false,
        keepFunctionIndentation: false,
        maxPreserveNewlines: 0,
        preserveNewlines: false,
        spaceBeforeConditional: true,
        spaceInParen: false,
        unescapeStrings: false,
        wrapLineLength: 0,
      }
    }))
    .pipe(gulp.dest('./'));
  gulp.src(config.app.templates.concat(['./src_frontend/index.html']), {
    base: './'
  })
    .pipe(prettify({
      html: {
        braceStyle: "collapse",
        indentChar: " ",
        indentScripts: "keep",
        indentSize: 4,
        maxPreserveNewlines: 0,
        preserveNewlines: false,
        unformatted: ["a", "sub", "sup", "b", "i", "u"],
        wrapLineLength: 0
      }
    }))
    .pipe(gulp.dest('./'));
});