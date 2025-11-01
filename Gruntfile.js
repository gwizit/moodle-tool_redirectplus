/**
 * Gruntfile for tool_redirectplus
 *
 * This file configures tasks to be run by Grunt
 * http://gruntjs.com/
 *
 * To use this file:
 * 1. Install node.js (http://nodejs.org/)
 * 2. Install grunt-cli globally: npm install -g grunt-cli
 * 3. Install local dependencies: npm install
 * 4. Run grunt: grunt amd
 */

module.exports = function(grunt) {
    'use strict';

    // Load all grunt tasks.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.initConfig({
        uglify: {
            amd: {
                files: [{
                    expand: true,
                    cwd: 'amd/src',
                    src: ['*.js'],
                    dest: 'amd/build',
                    ext: '.min.js',
                    extDot: 'last'
                }],
                options: {
                    banner: '',
                    compress: {
                        drop_console: false
                    },
                    mangle: true,
                    preserveComments: false
                }
            }
        },

        watch: {
            amd: {
                files: ['amd/src/*.js'],
                tasks: ['uglify:amd']
            }
        }
    });

    // Register tasks.
    grunt.registerTask('amd', ['uglify:amd']);
    grunt.registerTask('default', ['amd']);
};
