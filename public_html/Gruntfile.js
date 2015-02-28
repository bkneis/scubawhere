'use strict';
 
module.exports = function (grunt) {
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    
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
        },
        concat: {
            basic: {
                src: [
                    'dashboard/js/Controllers/*.js',
                    'dashboard/js/main.js',
                    'dashboard/js/ui.js',
                    'dashboard/js/navigation.js',
                    'dashboard/js/validate.js',
                    'dashboard/js/tour.js',

                ],
                dest: 'dashboard/js/scubawhere.js'
            }
        },
        uglify: {
            basic: {
                files: {
                    'dashboard/js/scubawhere.min.js': ['dashboard/js/scubawhere.js']
                }
            }
        }
    });

    grunt.registerTask('default', ['watch']);
    
    grunt.registerTask('dev', ['concat']);
    grunt.registerTask('production', ['concat', 'uglify']);
};