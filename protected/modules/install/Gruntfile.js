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
                dest: '<%= paths.css %>/install.css'
            }
        },
        cssmin : {
            css:{
                src: '<%= paths.css %>/install.css',
                dest: '<%= paths.css %>/install.min.css'
            }
        },
        watch: {
          files: ['<%= paths.ccss %>/*'],
          tasks: ['concat', 'cssmin']
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.registerTask('default', [ 'concat:css', 'cssmin:css' ]);
};
