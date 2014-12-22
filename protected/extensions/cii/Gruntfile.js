module.exports = function(grunt) {

    // Register the NPM tasks we want
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-bower-task');

    // Register the tasks we want to run
    grunt.registerTask('default', [
        'bower:install',
        'copy:jquery',
        'copy:comments',
        'copy:ciimscomments',
        'cssmin:ciimscomments',
        'concat:css',
        'concat:js',
        'cssmin:css',
	    'uglify:js',
        'uglify:comments'
    ]);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        paths: {
            assets: 'assets',
            bower: 'bower_components',
            lib: '<%= paths.assets %>/lib',
            css : '<%= paths.assets %>/css',
            js: '<%= paths.assets %>/js',
            dist: '<%= paths.assets %>/dist',
        },

        bower: {
          install: {
            options: {
              targetDir: "assets/lib"
            }
          }
        },

        copy: {
            jquery: {
                expand: true,
                flatten: true,
                src: "bower_components/jquery/dist/*",
                dest: "<%= paths.dist %>"
            },
            comments: {
                expand: true,
                flatten: true,
                src: "assets/js/**",
                dest: "<%= paths.dist %>"
            },
            ciimscomments: {
                expand: true,
                flatten: true,
                src: "assets/css/ciimscomments.css",
                dest: "<%= paths.dist %>"
            },
        },

        concat: {
            css: {
                src: [
                    '<%= paths.css %>/*',
                    '<%= paths.lib %>/*/*.css'
                ],
                dest: '<%= paths.dist %>/cii.css'
            },
            js : {
                src: [
                    '<%= paths.lib %>/marked/lib/marked.js',
                    '<%= paths.lib %>/md5-js/md5.js',
                    '<%= paths.lib %>/date.format/date.format.js',
                ],
                dest: '<%= paths.dist %>/cii.js'
            }
        },
        cssmin : {
            css:{
                src: '<%= paths.dist %>/cii.css',
                dest: '<%= paths.dist %>/cii.min.css'
            },
            ciimscomments: {
                src: '<%= paths.dist %>/ciimscomments.css',
                dest: '<%= paths.dist %>/ciimscomments.min.css'
            },
        },
        uglify: {
            comments: {
                files: {
                    '<%= paths.dist %>/ciimscomments.min.js' : ['<%= paths.dist %>/ciimscomments.js'],
                    '<%= paths.dist %>/ciidisqus.min.js' : ['<%= paths.dist %>/ciidisqus.js'],
                    '<%= paths.dist %>/disqus.min.js' : ['<%= paths.dist %>/disqus.js'],
                    '<%= paths.dist %>/discourse.min.js' : ['<%= paths.dist %>/discourse.js']
                }
            },
            js: {
                files: {
                    '<%= paths.dist %>/cii.min.js' : ['<%= paths.dist %>/cii.js'],
                    '<%= paths.dist %>/ciianalytics.min.js' : ['<%= paths.dist %>/ciianalytics.js'],
                    '<%= paths.dist %>/jquery.min.js' : ['<%= paths.dist %>/jquery.js']
                }
            }
        },
        watch: {
          files: ['<%= paths.css %>/*', '<%= paths.js %>/*', '<%= paths.lib %>/*'],
          tasks: ['default']
        },
    });
};
