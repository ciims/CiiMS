# What is the branch in your Git repository that will be deployed to the server?
set :branch, "#{stage}"

# The application webroot
set :webroot, "var/www"

set :site_root, "/#{webroot}/#{application}/#{stage}"

# What is the user/password that will connect to your Development server?
set :sudo_user, "deployment"
set :user, "deployment"
set :sshgroup, 'www-data'

# What is the directory path used to store your project on the remote server?
set :deploy_to, "#{site_root}/deployments"

# Setup Directories
task :setup_directories do
  run "test -d '#{deploy_to}/persistent' || #{try_sudo} mkdir -p '#{deploy_to}/persistent'"
  run "test -d '#{deploy_to}/persistent/config' || #{try_sudo} mkdir -p '#{deploy_to}/persistent/config'"
end

# Fix Permissions
task :fix_permissions do
    run "#{try_sudo} chown -R #{sudo_user}:#{sshgroup} #{deploy_to}"
    run "#{try_sudo} chmod -R 755 #{deploy_to}"
end

# Copy the config directories over to the persistent directory, and re-link the directories
task :move_configs do
	run "#{try_sudo} cp '#{deploy_to}/persistent/config/main.php' '#{release_path}/protected/config/main.php'"
	run "#{try_sudo} rm -rf '#{release_path}/protected/modules/admin/views/default/cards/001-server.php'"
end

task :migrate do
	run "cd #{release_path}/protected/ && php yiic.php migrate --interactive=0"
end

after "deploy:setup", :setup_directories
after "deploy:setup", :fix_permissions

before "deploy:create_symlink", :move_configs