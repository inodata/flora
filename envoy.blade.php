@servers(['servergrove' => ['root@inodata.mx']])

@setup
  $root_path = '/var/www/vhosts/flora.inodata.mx/flora';
@endsetup

@story('deploy', ['on' => 'servergrove'])
  git
  composer
  update_db
  install_assets
@endstory

@task('git')
  cd {{ $root_path }}
  @if(!$branch)
    git pull origin master
  @else
    git pull origin {{ $branch }}
  @endif
@endtask

@task('composer')
  cd {{ $root_path }}
  php composer install --no-dev --optimize-autoloader
@endtask

@task('update_db')
  cd {{ $root_path }}
  php app/console doctrine:schema:update --force
@endtask

@task('install_assets')
  cd {{ $root_path }}
  php app/console assetic:dump --env=prod
  php app/console assets:install --env=prod --symlink
  php app/console cache:clear --env=prod
  chmod -R 777 app/cache/ app/logs/
@endtask
