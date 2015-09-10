set -o errexit

git pull

# Pull updates for ezmonitor
# git --git-dir=/home/scubawhere/applications/rms/workbench/sisou/ezmonitor/.git --work-tree=/home/scubawhere/applications/rms/workbench/sisou/ezmonitor pull

php artisan migrate --force
php artisan optimize

# Update composer
php composer self-update

# Run asset tasks (concatenation, minimisation)
grunt --gruntfile ./public_html/Gruntfile.js
