.SILENT: ;

test: cs-check stan check-deprecations test-phpunit

cs-check:
	vendor/bin/php-cs-fixer fix --diff --dry-run

test-phpunit:
	vendor/bin/phpunit

check-deprecations:
	php bin/check-deprecations.php

stan:
	vendor/bin/phpstan analyse --no-progress
