phpstan:
	APP_ENV=test bin/phpstan.sh

ecs:
	APP_ENV=test bin/ecs.sh --clear-cache

fix:
	APP_ENV=test bin/ecs.sh --fix

install:
	composer install --no-interaction --no-scripts
	rm -fr tests/Application/public/media/cache && mkdir -p tests/Application/public/media/cache && chmod -R 777 tests/Application/public/media
	rm -fr tests/Application/var && mkdir -p tests/Application/var/log && chmod -R 777 tests/Application/var
	touch tests/Application/var/log/test.log && chmod 777 tests/Application/var/log/test.log

backend:
	APP_ENV=test tests/Application/bin/console doctrine:database:drop --force || true
	APP_ENV=test tests/Application/bin/console doctrine:database:create --no-interaction
	APP_ENV=test tests/Application/bin/console doctrine:migrations:migrate --no-interaction
	APP_ENV=test tests/Application/bin/console doctrine:schema:update --force --complete --no-interaction
	APP_ENV=test tests/Application/bin/console doctrine:migration:sync-metadata-storage
	APP_ENV=test tests/Application/bin/console sylius:install --no-interaction
	APP_ENV=test tests/Application/bin/console sylius:fixtures:load default --no-interaction

frontend:
	APP_ENV=test tests/Application/bin/console assets:install
	(cd tests/Application && yarn install --pure-lockfile)
	(cd tests/Application && GULP_ENV=prod yarn build)

lint:
	APP_ENV=test bin/symfony-lint.sh

behat:
	APP_ENV=test bin/behat.sh

init: install backend frontend

tests: phpstan ecs lint behat

static: phpstan ecs lint

ci: init static behat

run:
	docker compose up --detach

php-bash:
	@make run
	docker compose exec --user 1000:1000 php bash
