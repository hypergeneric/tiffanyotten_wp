const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const browserSync = require('browser-sync').create();
const autoprefixer = require('gulp-autoprefixer');
const uglify = require('gulp-uglify-es').default;
const jshint = require('gulp-jshint');
const rename = require('gulp-rename');
const cssnano = require('cssnano');
const postcss = require('gulp-postcss');
const concat = require('gulp-concat');
const order = require("gulp-order");
const sourcemaps = require('gulp-sourcemaps');
const addsrc = require('gulp-add-src');
const notifier = require('node-notifier');
const fs = require('fs');
const path = require('path');

let config;
try {
  config = require('./.config');
} catch (error) {
  config = require('./.config.default');
}

function toProjectRelativePath(fullPath) {
    const projectRoot = process.cwd(); // Gets the Gulp execution (project root) directory
    const normalizedPath = fullPath.replace(/\\/g, '/'); // Ensure cross-platform compatibility
    const relativePath = normalizedPath.replace(projectRoot, ''); // Remove the project root part
    // Assuming 'blocks' is a direct subdirectory of the project root
    // This will extract the path relative to 'blocks'
    const blocksRelativePath = relativePath.split('/blocks/')[1] || relativePath;
    // Trim any leading slashes from the blocksRelativePath
    const trimmedPath = blocksRelativePath.replace(/^\/+/, '');
    return trimmedPath;
}

// Helper function to find the nearest parent SCSS/CSS file
function getNearestParent(filePath, extensions = "scss,css") {
    // Convert the comma-delimited string into an array
    const extArray = extensions.split(',').map(ext => ext.trim());
    let currentDir = path.dirname(filePath);
    while (currentDir.includes('blocks')) {
        const folderName = path.basename(currentDir);
        for (const ext of extArray) {
            const mainFilePath = path.join(currentDir, `${folderName}.${ext}`);
            if (fs.existsSync(mainFilePath)) {
                return mainFilePath;
            }
        }
        currentDir = path.dirname(currentDir);
    }
    return null;
}

function refreshMtime(files) {
    const now = new Date();
    files.forEach(function (file) {
        try { fs.utimesSync(file, now, now); } catch (err) {}
    });
}

function processCss(src) {
    let stream = gulp.src(src.source);
    if ( config.production ) {
        stream = stream.pipe(sass().on('error', function () {}));
    } else {
        stream = stream
            .pipe(sourcemaps.init())
            .pipe(sass().on('error', function (err) {
                console.error('[CSS error]', err.message);
                notifier.notify({
                    title: '[CSS error]',
                    message: err.message
                });
                this.emit('end');
            }));
    }
    stream = stream.pipe(autoprefixer('last 4 version'));
    if ( src.prepend && src.prepend.length > 0 ) {
        stream = stream.pipe(addsrc.prepend(src.prepend));
    }
    if ( src.append && src.append.length > 0 ) {
        stream = stream.pipe(addsrc.append(src.append));
    }
    stream = stream.pipe(concat(src.filename));
    if ( config.production ) {
        stream = stream
            .pipe(postcss([cssnano()]))
            .pipe(rename({ suffix: '.min' }))
            .pipe(gulp.dest(src.destination));
    } else {
        stream = stream
            .pipe(sourcemaps.write('.'))
            .pipe(gulp.dest(src.destination + '-dev'))
            .pipe(browserSync.stream());
    }
    stream.on('end', function () {
        const dest    = src.destination + ( config.production ? '' : '-dev' );
        const outName = config.production ? src.filename.replace(/\.css$/, '.min.css') : src.filename;
        refreshMtime([ path.join(dest, outName) ]);
    });
    return stream;
}

function processCSSIndividual(file) {
    let relativePath  = toProjectRelativePath( file.path );
    let destPath      = "assets/css" + ( config.production ? '' : '-dev' ) + "/blocks/" + relativePath;
    let destDirectory = destPath.substring(0, destPath.lastIndexOf('/'));
    let stream = gulp.src(file.path)
    if ( config.production ) {
        stream = stream.pipe(sass().on('error', function () {}));
    } else {
        stream = stream
            .pipe(sourcemaps.init())
            .pipe(sass().on('error', function (err) {
                console.error('[CSS error]', err.message);
                notifier.notify({
                    title: '[CSS error]',
                    message: err.message
                });
                this.emit('end');
            }));
    }
    stream = stream.pipe(autoprefixer('last 4 version'));
    if ( config.production ) {
        stream = stream
            .pipe(postcss([cssnano()]))
            .pipe(rename({ suffix: '.min' }))
            .pipe(gulp.dest(destDirectory));
    } else {
        stream = stream
            .pipe(sourcemaps.write('.'))
            .pipe(gulp.dest(destDirectory))
            .pipe(browserSync.stream());
    }
    stream.on('end', function () {
        const outName = path.basename(file.path, '.scss') + ( config.production ? '.min.css' : '.css' );
        refreshMtime([ path.join(destDirectory, outName) ]);
    });
    return stream;
}

function processJs(src) {
    let stream = gulp.src(src.source)
        .pipe(jshint('.jshintrc'))
        .pipe(jshint.reporter('default', { emitError: true }));
    if ( ! config.production ) {
        stream = stream
            .pipe(sourcemaps.init())
            .pipe(jshint.reporter('fail').on('error', function (err) {
                console.error('[JS error]', err.message);
                notifier.notify({
                    title: '[JS error]',
                    message: err.message
                });
                this.emit('end');
            }));
    }
    if (src.order && src.order.length > 0) {
        stream = stream.pipe(order(src.order));
    }
    if (src.prepend && src.prepend.length > 0) {
        stream = stream.pipe(addsrc.prepend(src.prepend));
    }
    if (src.append && src.append.length > 0) {
        stream = stream.pipe(addsrc.append(src.append));
    }
    stream = stream.pipe(concat(src.filename));
    if (src.minify) {
        stream = stream
        .pipe(uglify().on('error', function (err) {
            notifier.notify({
                title: 'JS Build Error',
                message: err.message
            });
            this.emit('end');
        }))
        .pipe(rename({ suffix: '.min' }))
    }
    if ( config.production ) {
        stream = stream.pipe(gulp.dest(src.destination));
    } else {
        stream = stream
            .pipe(sourcemaps.write('.'))
            .pipe(gulp.dest(src.destination + '-dev'))
            .pipe(browserSync.stream());
    }
    return stream;
}

function processJsIndividual(file) {
    let relativePath  = toProjectRelativePath( file.path );
    let destPath      = "assets/js" + ( config.production ? '' : '-dev' ) + "/blocks/" + relativePath;
    let destDirectory = destPath.substring(0, destPath.lastIndexOf('/'));
    let stream = gulp.src(file.path)
        .pipe(jshint('.jshintrc'))
        .pipe(jshint.reporter('default', { emitError: true }));
    if ( ! config.production ) {
        stream = stream
        .pipe(sourcemaps.init())
        .pipe(jshint.reporter('fail').on('error', function (err) {
            console.error('[JS error]', err.message);
            notifier.notify({
                title: '[JS error]',
                message: err.message
            });
            this.emit('end');
        }));
    }
    if ( config.production ) {
        stream = stream
        .pipe(uglify().on('error', function (err) {
            notifier.notify({
                title: 'JS Build Error',
                message: err.message
            });
            this.emit('end');
        }))
        .pipe(rename({ suffix: '.min' }));
    }
    if ( config.production ) {
        stream = stream.pipe(gulp.dest(destDirectory));
    } else {
        stream = stream
            .pipe(sourcemaps.write('.'))
            .pipe(gulp.dest(destDirectory))
            .pipe(browserSync.stream());
    }
    return stream;
}

function jsblocks(cb) {
    gulp.src('blocks/**/*.js')
        .on('data', function(file) {
            processJsIndividual(file);
        });
    cb();
}

function cssblocks(cb) {
    gulp.src('blocks/**/*.{scss,css}')
        .on('data', function(file) {
            processCSSIndividual(file);
        });
    cb();
}

function css() {
    const tasks = config.css.sources.map(processCss);
    return Promise.all(tasks);
}

function js() {
    const tasks = config.js.sources.map(processJs);
    return Promise.all(tasks);
}

function build(done) {
    return gulp.parallel(css, js, cssblocks, jsblocks)(done);
}

function browserSyncInit(done) {
    browserSync.init({
        proxy: config.local_url,
    });
    done();
}

function bsReload(done) {
    browserSync.reload();
    done();
}

function watch() {
    gulp.watch("src/scss/**/*.scss", css);
    gulp.watch("src/js/**/*.js", js);
    gulp.watch("blocks/**/*.{scss,css}").on('change', function(relativePath) {
        let absolutePath = path.resolve(process.cwd(), relativePath.replace(/\\/g, '/'));
        // Check to see if it's a descendent
        if ( ! absolutePath.match(/(blocks\/[^/]+)\/[^/]+\.scss$/) ) {
            absolutePath = getNearestParent(absolutePath, "scss,css");
        }
        if (absolutePath) {
            processCSSIndividual({ path: absolutePath });
        }
    });
    gulp.watch("blocks/**/*.js").on('change', function(relativePath, stats) {
        let absolutePath = process.cwd().replace(/\\/g, '/') + '/' + relativePath.replace(/\\/g, '/');
        processJsIndividual({ path: absolutePath });
    });
    gulp.watch("./**/*.php", bsReload);
}

exports.css = css;
exports.js = js;
exports.jsblocks = jsblocks;
exports.cssblocks = cssblocks;
exports.build = build;
exports.default = gulp.series(build, gulp.parallel(browserSyncInit, watch));