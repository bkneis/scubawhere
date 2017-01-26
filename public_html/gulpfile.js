/*
 Karma / Mocha Unit Testing
 */

//ES6
import { Server } from 'karma';

// ES5
// var Server = require('karma').Server; 

gulp.task('karma:watch', function (done) {
    new Server({
        configFile: __dirname + '/karma.config.js',
        singleRun: false,
        autoWatch: true,
        browsers: ['PhantomJS']
    }, done).start();
});

gulp.task('karma', function (done) {
    new Server({
        configFile: __dirname + '/karma.config.js',
        singleRun: true,
        autoWatch: false
    }, done).start();
});