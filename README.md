# Getting started

## Build the app

```
make install
```

## Launch docker-compose stack

```
make run
```

## Launch docker-compose stack

| service    | port | identifiant | password |
|------------|------|-------------|----------|
| POSTGRESQL | 5432 | vintud      | vintud   |
| ADMINER    | 8080 | vintud      | vintud   |
| SYMFONY    | 8000 |             |          |

## Generate fixtures into your database

```
make fixtures
```

## Lint code before commit

```
make prettier
```
