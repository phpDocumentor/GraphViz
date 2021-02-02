.PHONY: install-phive
install-phive:
	mkdir tools; \
	wget -O tools/phive.phar https://phar.io/releases/phive.phar; \
	wget -O tools/phive.phar.asc https://phar.io/releases/phive.phar.asc; \
	gpg --keyserver pool.sks-keyservers.net --recv-keys 0x9D8A98B29B2D5D79; \
	gpg --verify tools/phive.phar.asc tools/phive.phar; \
	chmod +x tools/phive.phar

.PHONY: setup
setup: install-phive
	docker run -it --rm -v${PWD}:/opt/project -w /opt/project phpdoc/phar-ga:latest php tools/phive.phar install --force-accept-unsigned
.PHONY: phpcbf
phpcbf:
	docker run -it --rm -v${CURDIR}:/opt/project -w /opt/project phpdoc/phpcs-ga:latest phpcbf ${ARGS}

.PHONY: phpcs
phpcs:
	docker run -it --rm -v${PWD}:/opt/project -w /opt/project phpdoc/phpcs-ga:latest -d memory_limit=1024M -s

.PHONY: phpstan
phpstan:
	docker run -it --rm -v${CURDIR}:/opt/project -w /opt/project phpdoc/phpstan-ga:latest analyse src tests --configuration phpstan.neon ${ARGS}

.PHONY: psalm
psalm:
	docker run -it --rm -v${CURDIR}:/data -w /data php:7.3 ./tools/psalm

.PHONY: test
test:
	docker run -it --rm -v${PWD}:/opt/project -w /opt/project php:7.2 vendor/bin/phpunit

.PHONY: pre-commit-test
pre-commit-test: phpcs phpstan psalm test

