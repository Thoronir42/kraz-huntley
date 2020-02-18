const gulp = require('gulp');

const sassTasks = require('./gulp/sass.gulp');
const jsSrc = require('./gulp/jsSources.gulp');
// const vendorTasks = require('./vendor.gulp');

gulp.task('sass', sassTasks.sass);

gulp.task('sass:watch', () => {
    return gulp.watch(sassTasks.watch, gulp.task('sass'));
});

gulp.task('js', jsSrc.concatSources);

gulp.task('js:watch', () => {
    return gulp.watch(jsSrc.sources, gulp.task('js'));
});

gulp.task('watch', () => {
    gulp.watch(sassTasks.watch, gulp.task('sass'));
    gulp.watch(jsSrc.sources, gulp.task('js'));
});

// gulp.task('vendor', vendorTasks.copyVendor);
