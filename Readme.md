## Tasks

### test
Run tests
```sh
docker run --rm -it --workdir=/var/www/php -v $(PWD):/var/www/php --entrypoint bash vladitot/php83-swow-ubuntu-local:v0.1.16 -c -l "composer install"
docker run --rm -it --workdir=/var/www/php -v $(PWD):/var/www/php --entrypoint bash vladitot/php83-swow-ubuntu-local:v0.1.16 -c -l "vendor/bin/phpunit"
```

### bash
Run bash shell
interactive: true
```sh
docker run --rm -it --workdir=/var/www/php -v $(PWD):/var/www/php --entrypoint bash vladitot/php83-swow-ubuntu-local:v0.1.16 -c -l "bash"
```

### debug
Run bash shell with xdebug
interactive: true
```sh
docker run --rm -e XDEBUG_SESSION=PHPSTORM -e XDEBUG_MODE=debug  -e PHP_IDE_CONFIG='serverName=anodio-core' -it --workdir=/var/www/php -v $(PWD):/var/www/php --entrypoint bash vladitot/php83-swow-ubuntu-local:v0.1.17 -c -l "bash"
```
