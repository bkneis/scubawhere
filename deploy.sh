trap 'exit 1' ERR

# Manually start ssh-agent and add deployment key to it
ssh-agent /bin/bash
ssh-add ~/.ssh/id_bitbucket_krystal

git pull origin development

# Exit the bash that has been started with the ssh-agent
exit

yes | php -d disable_functions= artisan migrate

php -d disable_functions= artisan optimize
