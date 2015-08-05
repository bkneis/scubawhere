set -o errexit

git pull

# Pull updates for ezmonitor
# git --git-dir=/home/scubawhe/workbench/sisou/ezmonitor/.git --work-tree=/home/scubawhe/workbench/sisou/ezmonitor pull

php -d disable_functions= artisan migrate --force
php -d disable_functions= artisan optimize
