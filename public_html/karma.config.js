// Karma configuration
module.exports = function(config) {
    config.set({
        // ... normal karma configuration
        files: [
            // all files ending in "_test"
            {pattern: 'test/*_test.js', watched: false},
            {pattern: 'test/**/*_test.js', watched: false}
            // each file acts as entry point for the webpack configuration
        ],

        preprocessors: {
            // add webpack as preprocessor
            'test/*_test.js': ['webpack'],
            'test/**/*_test.js': ['webpack']
        },

        webpack: {
            // karma watches the test entry points
            // (you don't need to specify the entry option)
            // webpack watches dependencies

            // webpack configuration
        },

        webpackMiddleware: {
            // webpack-dev-middleware configuration
            // i. e.
            stats: 'errors-only'
        }
    });
};

/*module.exports = function(config) {
    config.set({

        basePath: '',

        frameworks: ['mocha'],

        files: [
            'node_modules/babel-polyfill/browser.js',
            'test/*.js'
        ],

        exclude: [

        ],

        preprocessors: {
            'test/*.js': ['webpack']
        },

        webpack: {
            resolve: {
                extensions: ["*", ".js", ".jsx", ".json", ".scss"]
            },
            module: {
                loaders: [            {
                    test: /\.js$|\.jsx$/,
                    exclude: /node_modules/,
                    loader: 'babel-loader'
                },
                    {
                        test: /\.json$/,
                        loader: 'json-loader'
                    }]
            }
        },

        webpackMiddleware: {
            noInfo: true
        },

        plugins: [
            require('karma-webpack'),
            require('karma-chrome-launcher'),
            require('karma-phantomjs-launcher'),
            require('karma-ie-launcher'),
            require('karma-mocha')
        ],

        reporters: ['progress'],

        port: 9876,

        colors: true,

        logLevel: config.LOG_INFO,

        autoWatch: false,

        browsers: ['PhantomJS', 'Chrome', 'Firefox', 'IE', 'IE9', 'Safari'],

        customLaunchers: {
            IE9: {
                base: 'IE',
                'x-ua-compatible': 'IE=EmulateIE9'
            }
        },

        singleRun: true,

        concurrency: Infinity
    })
}*/