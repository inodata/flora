set :application, "Flora Project"
set :domain,      "flora.inodata.com.mx"
set :user_id,	  "56663"
set :user,        "komodot.com" #SSH user on production server
set :deploy_to,   "/home/#{user_id}/users/.home/domains/#{domain}/flora"
set :app_path,    "app"

set :repository,  "file:///home/egarcia/workspace/flora"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`
set :deploy_via,  :capifony_copy_local
set :use_composer,     true
set :use_composer_tmp, true

set  :use_sudo,   false
set  :keep_releases,  3

task :upload_parameters do
  origin_file = "app/config/parameters.yml"
  destination_file = latest_release + "/app/config/parameters.yml" # Notice the latest_release

  try_sudo "mkdir -p #{File.dirname(destination_file)}"
  top.upload(origin_file, destination_file)
end

before "deploy:share_childs", "upload_parameters"

# Symfony2
set :model_manager, "doctrine"                   # Or: `propel`
role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server
set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,   [app_path + "/logs", web_path + "/uploads", "vendor"]

set :composer_options,  "--no-scripts --no-dev --verbose --prefer-dist --optimize-autoloader"
# default values "--no-dev --verbose --prefer-dist --optimize-autoloader --no-progress"

set :writable_dirs,       ["app/cache", "app/logs"]
set :webserver_user,      "www-data"
set :permission_method,   :acl
set :use_set_permissions, true


# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL