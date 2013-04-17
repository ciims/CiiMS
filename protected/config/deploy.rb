###################################################################
# PROJECT-SPECIFIC VARIABLES. MUST EDIT PER PROJECT.
###################################################################

# What is the case-sensitive name of the git-hub project?
# This will be used when setting up folders on the target system.
set :application, 'CiiMS'

set :repository, "https://github.com/charlesportwoodii/#{application}.git"

set :application, application.downcase

###################################################################
# UNIVERSAL VARIABLES/TASKS. NEVER EDIT BELOW THIS LINE.
###################################################################
#
# No default PTY. Prior to 2.1, Capistrano would request a pseudo-tty for each
# command that it executed. This had the side-effect of causing the profile
# scripts for the user to not be loaded. Well, no more! As of 2.1, Capistrano no
# longer requests a pty on each command, which means your .profile (or .bashrc,
# or whatever) will be properly loaded on each command! Note, however, that some
# have reported on some systems, when a pty is not allocated, some commands will
# go into non-interactive mode automatically. If you’re not seeing commands
# prompt like they used to, like svn or passwd, you can return to the previous
# behavior by adding the following line to your capfile:
default_run_options[:pty] = true

# Disable sh wrapping. Some shared hosts do not allow the POSIX shell to be used
# to execute arbitrary commands, which is what Capistrano has done since 2.0.
# If you’re on such a host, you can add the following line to your capfile:
default_run_options[:shell] = false

# What version control solution does the project use?
set :scm, :git
set :git_enable_submodules, true

# Verbose output?
set :scm_verbose, true

# Is sudo required to manipulate files on the remote server?
set :use_sudo, false

# How are the project files being transferred to the remote server?
set :deploy_via, :remote_cache

# Used if deploy_via is :copy
set :copy_compression, :gzip
set :copy_cache, true
set :copy_exclude, [".git*", ".svn*", ".DS_Store", "*.md", "*.textile", "*apfile", "*.rb", "*.log", "nbproject", "*.conf", "install.php", "index-test.php"]

# Define the stages used.
set :stages, %w(develop master) # MUST NOT HAVE A STAGE NAMED "STAGE"
set :stage_dir, "protected/config/deploy"
set :default_stage, "master" # If we only do “cap deploy” this will be the stage used.

# How many releases should be kept?
set :keep_releases, 3

# The 'capistrano-ext' gem allows for multiple
require 'capistrano/ext/multistage' #yes. First we set and then we require.

# Remove older releases. By default, it will remove all older then the #{keep_releases} value.
after :deploy, 'deploy:cleanup'
role(:web) { deployment_host.split(/,/) }