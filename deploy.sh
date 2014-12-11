function deploy_success()
{
# Prepare Slack webhook
cat << "EOT" > COMMENT
%COMMENT%
EOT

    revision='%REVISION%'
    shortrevision=${revision:0:7}

    COMMENT=$( cat COMMENT | sed ':a;N;$!ba;s/\n/\\n/g' ) # Replace newlines with the string '\n'
    curl -X POST --data-urlencode 'payload={"username": "Dploy.io", "icon_url": "https://pbs.twimg.com/profile_images/378800000469704057/647e5983f89f6052c5f234947edb2774.png", "attachments":[{"fallback":"<https://bitbucket.org/scubawhere/system/commits/%REVISION%|'"$shortrevision"'> by %USER_NAME% has been deployed to %ENV_NAME%.", "pretext":"<https://bitbucket.org/scubawhere/system/commits/%REVISION%|'"$shortrevision"'> by %USER_NAME% has been deployed to %ENV_NAME%.", "color":"good", "fields":[{"title":"Deployment successful", "value":"'"$COMMENT"'", "short":false}]}]}' https://scubawhere.slack.com/services/hooks/incoming-webhook?token=qVHxaGtxuYL6a4mLLyPwVgsc
}

function deploy_failure()
{
# Prepare Slack webhook
cat << "EOT" > COMMENT
%COMMENT%
EOT

    revision='%REVISION%'
    shortrevision=${revision:0:7}

    COMMENT=$( cat COMMENT | sed ':a;N;$!ba;s/\n/\\n/g' ) # Replace newlines with the string '\n'
    curl -X POST --data-urlencode 'payload={"username":"Dploy.io","icon_url":"https://pbs.twimg.com/profile_images/378800000469704057/647e5983f89f6052c5f234947edb2774.png","attachments":[{"fallback":"<https://scubawhere.dploy.io|Visit the Dashboard> to view the deployment log","pretext":"<https://scubawhere.dploy.io|Visit the Dashboard> to view the deployment log","color":"danger","fields":[{"title":"Deployment failed","value":"'"$shortrevision"' by %USER_NAME%: '" $COMMENT "'","short":false}]}]}' https://scubawhere.slack.com/services/hooks/incoming-webhook?token=qVHxaGtxuYL6a4mLLyPwVgsc

    exit
}

trap deploy_failure ERR

# Manually start ssh-agent and add deployment key to it
ssh-agent /bin/bash
ssh-add ~/.ssh/id_bitbucket_krystal

git pull origin development

# Exit the bash that has been started with the ssh-agent
exit

yes | php -d disable_functions= artisan migrate

php -d disable_functions= artisan optimize

deploy_success
