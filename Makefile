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


.PHONY: build-test-image
build-test-image:
	docker build -t php-graphviz -f tests/Resources/Dockerfile tests/Resources

.PHONY: phpcs
phpcs: build-test-image
	docker run -it --rm -v${CURDIR}:/data -w /data php-graphviz vendor/bin/phpcs

.PHONY: phpstan
phpstan: build-test-image
	docker run -it --rm -v${CURDIR}:/data -w /data php-graphviz ./vendor/phpstan/phpstan/phpstan analyse src ${ARGS}

.PHONY: psalm
psalm: build-test-image
	docker run -it --rm -v${CURDIR}:/data -w /data php-graphviz vendor/bin/psalm.phar

.PHONY: test
test: build-test-image
	docker run -it --rm -v${PWD}:/opt/project -w /opt/project php-graphviz ./vendor/bin/phpunit

.PHONY: pre-commit-test
pre-commit-test: phpcs phpstan psalm test

