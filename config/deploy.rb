set :application, "rps_theme"
set :repository,  ""
set :scm, :git
set :apc_webroot,  ""
set :opc_webroot,  ""
set :url_base, ""

# set :scm, :git # You can set :scm explicitly or Capistrano will make an intelligent guess based on known version control directory names
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

role :web, "rps.avirtualhome.com"                          # Your HTTP server, Apache/etc
role :app, "rps.avirtualhome.com"                          # This may be the same as your `Web` server
role :db,  "rps.avirtualhome.com", :primary => true # This is where Rails migrations will run

set :deploy_to, "/home/pdoes/capistrano/rps/theme"
set :use_sudo, false
set :deploy_via, :remote_cache
set :copy_exclude, [".git", ".gitmodules", ".DS_Store", ".gitignore", "sass", "Capfile", "config"]
set :keep_releases, 5

set :branch, fetch(:branch, "develop")

# if you want to clean up old releases on each deploy uncomment this:
after "deploy:restart", "deploy:cleanup"
after "deploy", "opc:clearCache"
#after "deploy:finalize_update", "db:update_database"

# If you are using Passenger mod_rails uncomment this:
# namespace :deploy do
#   task :start do ; end
#   task :stop do ; end
#   task :restart, :roles => :app, :except => { :no_release => true } do
#     run "#{try_sudo} touch #{File.join(current_path,'tmp','restart.txt')}"
#   end
# end
#
