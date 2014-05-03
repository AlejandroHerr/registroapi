var p = require('./package.json')
var config = require('./gulp.config.json')
var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var cssmin = require('gulp-cssmin');
var rename = require('gulp-rename');
//var uncss = require('gulp-uncss');
var ngmin = require('gulp-ngmin');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var html2js = require('gulp-html2js');
var htmlmin = require('gulp-htmlmin');
var clean = require('gulp-clean');
var inject = require('gulp-inject');
var watch = require('gulp-watch');

/***BUILD***/
gulp.task('clean-build-js', function () {
    return gulp.src([config.build_assets_dir + '/*.js'], {
            read: false,
            force: true
        })
        .pipe(clean());
});
gulp.task('clean-build-css', function () {
    return gulp.src([config.build_assets_dir + '/*.css'], {
            read: false,
            force: true
        })
        .pipe(clean());
});
gulp.task('clean-build', ['clean-build-js', 'clean-build-css'], function () {});
gulp.task('css-build', function () {
    return gulp.src(config.src_dir + '/less/main.less')
        .pipe(less())
        .pipe(rename({
            basename: p.name,
            suffix: '-' + p.version
        }))
        .pipe(gulp.dest(config.build_assets_dir))
});
gulp.task('templates', function () {
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
gulp.task('js-build', ['templates'], function () {
    return gulp.src(config.vendor.js.concat(config.app.js, [config.build_assets_dir + "/delete/templates.js"]))
        .pipe(ngmin())
        .pipe(concat(p.name + '-' + p.version + '.js'))
        .pipe(gulp.dest(config.build_assets_dir));
});
gulp.task('build-app', ['clean-build', 'js-build', 'css-build'], function () {});
gulp.task('build-index', [], function () {
    return gulp.src([config.src_dir + '/index.html'])
        .pipe(inject(gulp.src([config.build_assets_dir + "/*.js", config.build_assets_dir + "/*.css"], {
            read: false
        }), {
            ignorePath: 'build/',
            addRootSlash: false
        }))
        .pipe(gulp.dest(config.build_dir));
});
gulp.task('clean_trash', function () {
    return gulp.src([config.build_assets_dir + '/delete'], {
            read: false,
            force: true
        })
        .pipe(clean());
});
gulp.task('build-assets', function () {
    return gulp.src(config.vendor.assets.concat(config.app.assets))
        .pipe(gulp.dest(config.build_assets_dir))
});
gulp.task('post-build', ['build-index', 'build-assets', 'clean_trash'], function () {});
gulp.task('build', ['build-app'], function () {
    return gulp.start('post-build');
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
    gulp.start('post-deploy');
});

/***DEFAULT***/
gulp.task('default', ['build'], function () {
    gulp.start('deploy');
    gulp.watch(config.src_dir + '/less/*.less', ['css-build']);
});

gulp.task('clean-all', function () {
    return gulp.src([config.build_dir, config.deploy_dir], {
            read: false,
            force: true
        })
        .pipe(clean());
});
//var watcher = gulp.watch(config.src_dir + '/less/*.less', ['css-build']);

