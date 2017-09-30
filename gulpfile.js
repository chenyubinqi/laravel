var gulp = require('gulp');
//加载gulp-load-plugins插件，并马上运行它
var plugins = require('gulp-load-plugins')();
var pump = require('pump');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var vueify = require("vueify");

gulp.task('browserify', function () {
    return browserify({
        'entries': 'resources/assets/js/app.js'
    })
    //require(*.vue)模板时需要vueify预处理，
    // 同时需要安装npm install --save-dev babel-preset-es2015 babel-plugin-transform-runtime
        .transform(vueify)
        .bundle()
        .pipe(source('app.js')) // gives streaming vinyl file object
        .pipe(buffer()) // <----- convert from streaming to buffered vinyl file object
        .pipe(gulp.dest('public/js'))
        .pipe(plugins.livereload());
});

gulp.task('js', ['browserify'], function (cb) {
    pump([
        gulp.src([
            'public/js/app.js',
            'node_modules/bootstrap-datetime-picker/js/bootstrap-datetimepicker.js',
            'node_modules/bootstrap-datetime-picker/js/locales/bootstrap-datetimepicker.zh-CN.js'
        ]),
        plugins.concat('app.js'),
        plugins.uglify(),
        gulp.dest('public/js')
    ], cb);
});

gulp.task('css', function () {
    gulp.src([
        'resources/assets/sass/app.scss'
    ])
        .pipe(plugins.sass())
        .pipe(gulp.dest('public/css'))
        .pipe(plugins.livereload());
    gulp.src([
        'public/css/app.css',
        'node_modules/bootstrap-datetime-picker/css/bootstrap-datetimepicker.css'
    ]).pipe(plugins.concat('app.css'))  // 合并匹配到的css文件并命名为 "app.css"
        .pipe(plugins.minifyCss()) //压缩css
        .pipe(gulp.dest('public/css'));
});

gulp.task('default', ['js', 'css']);

gulp.task('watch', function () {
    plugins.livereload.listen(); //要在这里调用listen()方法
    gulp.watch(['resources/assets/js/*.js', 'resources/assets/sass/*.scss'], ['js', 'css']);
});