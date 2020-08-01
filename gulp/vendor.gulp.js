const gulp = require('gulp');

const dependencies = [
    {
        src: 'node_modules/jquery/dist/**/*',
        dst: 'jquery',
    },
    {
        src: 'node_modules/bootstrap/dist/**/*',
        dst: 'bootstrap',
    },
    {
        src: 'node_modules/summernote/dist/**/*',
        dst: 'summernote',
    },
    {
        src: 'node_modules/naja/dist/**/*',
        dst: 'naja',
    },
];


module.exports.copyVendor = async () => {
    const folderTasks = [];

    dependencies.forEach((dependency) => {
        let task = gulp.src(dependency.src, dependency.srcOptions)
            .pipe(gulp.dest('www/vendor/' + dependency.dst));

        folderTasks.push(task);
    });

    await Promise.all(folderTasks);
};
