set -o errexit

# Manually start ssh-agent and add deployment key to it
eval `ssh-agent -s`
ssh-add ~/.ssh/id_bitbucket_krystal

git pull

# Pull updates for ezmonitor
git --git-dir=/home/scubawhe/workbench/sisou/ezmonitor/.git --work-tree=/home/scubawhe/workbench/sisou/ezmonitor pull

# Kill the ssh-agent
ssh-agent -k

php -d disable_functions= artisan migrate --force
php -d disable_functions= artisan optimize
