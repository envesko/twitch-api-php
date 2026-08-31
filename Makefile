.SILENT: ;

test: cs-check stan check-deprecations test-phpunit test-phpspec

cs-check:
	vendor/bin/php-cs-fixer fix --diff --dry-run

test-phpunit:
	vendor/bin/phpunit

test-phpspec:
	vendor/bin/phpspec run

check-deprecations:
	php bin/check-deprecations.php

stan:
	vendor/bin/phpstan analyse --no-progress
