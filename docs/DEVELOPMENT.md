# Development

## Coding standards

```sh
docker run --rm --volume ${PWD}:/app itkdev/php8.1-fpm:alpine composer2 install
docker run --rm --volume ${PWD}:/app itkdev/php8.1-fpm:alpine composer2 coding-standards-check

docker run --rm --volume ${PWD}:/app itkdev/php8.1-fpm:alpine composer2 coding-standards-apply
```

```sh
docker run --rm --volume ${PWD}:/app --workdir /app node:18 yarn install
docker run --rm --volume ${PWD}:/app --workdir /app node:18 yarn coding-standards-check
```

## Code analysis

```sh
docker run --rm --volume ${PWD}:/app itkdev/php8.1-fpm:alpine composer2 install
docker run --rm --volume ${PWD}:/app itkdev/php8.1-fpm:alpine composer2 code-analysis
```
