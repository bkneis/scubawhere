'use strict';
 
module.exports = function (grunt) {
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    
    grunt.initConfig({
        watch: {
            less: {
               files: ["dashboard/common/less/*.less", ],
                tasks: ["less"] 
            },
            /*jshint: {
               files: "<%= jshint.files %>",
                tasks: ["jshint"] 
            },*/
        },
        less: {
            development: {
                options: {
                    paths: ["dashboard/common/less/"],
                },
                files: {
                    "dashboard/common/css/bootstrap-scubawhere.css": "dashboard/common/less/base.less"
                }
            },
        },
        jshint: {
            files: ['Gruntfile.js', 'dashboard/js/**/*.js', 'dashboard/tabs/**/*.js']
        },
        cssmin: {
            target: {
                files: {
                    'dashboard/common/css/bootstrap-scubawhere.min.css': ['dashboard/common/css/bootstrap-scubawhere.css']
                }
            }
        }
    });

    grunt.registerTask('default', ['watch']);
};