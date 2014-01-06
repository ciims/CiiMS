module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        paths: {
            css : 'assets/css',
            ccss : 'assets/css/src'
        },
        concat: {
            css: {
                src: [
                    'assets/css/src/*'
                ],
                dest: '<%= paths.css %>/theme.css'
            }
        },
        cssmin : {
            css:{
                src: '<%= paths.css %>/theme.css',
                dest: '<%= paths.css %>/theme.min.css'
            }
        },
        uglify: {
            js: {
                files: {
                    'assets/js/theme.min.js' : ['assets/js/theme.js']
                }
            }
        },
        watch: {
          files: ['<%= paths.ccss %>/*', 'assets/js/theme.js'],
          tasks: ['concat', 'cssmin']
        },
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.registerTask('default', [ 'concat:css', 'cssmin:css', 'uglify:js' ]);
};
