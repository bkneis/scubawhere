'use strict';
 
module.exports = function (grunt) {
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    
    grunt.initConfig({
        watch: {
            less: {
               files: ["common/less/*.less", ],
                tasks: ["less"] 
            },
            jshint: {
               files: "<%= jshint.files %>",
                tasks: ["jshint"] 
            },
        },
        less: {
            development: {
                options: {
                    paths: ["common/less/"],
                },
                files: {
                    "common/css/bootstrap-scubawhere.css": "common/less/base.less"
                }
            },
        },
        jshint: {
            files: ['Gruntfile.js', 'common/**/*.js', 'dashboard/js/**/*.js', 'dashboard/tabs/**/*.js']
        }
    });
     grunt.registerTask('default', ['watch']);
};