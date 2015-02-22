# Manually start ssh-agent and add deployment key to it
ssh-agent /bin/bash
ssh-add ~/.ssh/id_bitbucket_krystal

git pull

# Pull updates for ezmonitor
git --git-dir=/home/scubawhe/workbench/sisou/ezmonitor/.git --work-tree=/home/scubawhe/workbench/sisou/ezmonitor pull

# Exit the bash that has been started with the ssh-agent
exit

php -d disable_functions= artisan migrate --force
php -d disable_functions= artisan optimize
