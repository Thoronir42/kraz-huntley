const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');

module.exports.watch = [
    './app/sass/*.scss',
];
module.exports.files = [
    './app/sass/*.scss',
];

module.exports.sass = async () => {
    await gulp.src(module.exports.files)
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('www/dist/css/', {sourcemaps: true}))
};
