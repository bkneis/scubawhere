'use strict';
 
module.exports = function (grunt) {
    // load all grunt tasks
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    
    grunt.initConfig({
        watch: {
            files: "common/less/*.less",
            tasks: ["less"]
        },
        less: {
            development: {
                options: {
                    paths: ["common/less/"],
                },
                files: {
                    "common/css/bootstrap-scubawhere.css": "common/less/main.less"
                }
            },
        },
    });
     grunt.registerTask('default', ['watch']);
};