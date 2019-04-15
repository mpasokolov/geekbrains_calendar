php init --env=Production --overwrite=All
php yii migrate/down 100 --interactive=0
php yii migrate/up --interactive=0
php yii migrate --migrationPath=@yii/rbac/migrations --interactive=0
php yii rbac rbac/index  --interactive=0
php yii cache/flush-all
php yii cache/flush-schema --interactive=0