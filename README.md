# BileMo

## Mobile phone wholesaler API <br>

<img src="https://symfony.com/images/logos/sf-positive.svg" alt="symfony-logo" width="50" />  

_developped with Symfony 5.2_  

[![Maintainability](https://api.codeclimate.com/v1/badges/739a36c564f73fe81ea6/maintainability)](https://codeclimate.com/github/LFZDavid/BileMo/maintainability)

---

## Technical Requirements
---
* PHP ( version >= 7.2.5 )
* Database : 
    * mariadb ( version >= 10.2 )
    <br>or 
    * mysql ( version >= 5.7 )
* composer ( version : >= 2 )

more infos : _[symfony documentation](https://symfony.com/doc/current/setup.html#technical-requirements)_

---

## Dependencies
* doctrine/doctrine-migrations-bundle : "^3.1"
* phpdocumentor/reflection-docblock : "^5.2"
* lexik/jwt-authentication-bundle : "^2.11"
* sensio/framework-extra-bundle : "^6.1"
* symfony/proxy-manager-bridge : "5.2.*"
* liip/test-fixtures-bundle : "^1.11"
* symfony/framework-bundle : "5.2.*"
* symfony/property-access : "5.2.*"
* symfony/security-bundle : "5.2.*"
* doctrine/doctrine-bundle : "^2.3"
* symfony/property-info : "5.2.*"
* nelmio/api-doc-bundle : "^3.0"
* doctrine/annotations : "^1.13"
* symfony/serializer : "5.2.*"
* symfony/validator : "5.2.*"
* symfony/console : "5.2.*"
* symfony/dotenv : "5.2.*"
* symfony/flex : "^1.3.1"
* symfony/yaml : "5.2.*"
* doctrine/orm : "^2.8"
---

## Installation
1. ### Get files : 
>```
>git clone https://github.com/LFZDavid/BileMo.git
>```

2. ### Install dependencies : 
>```
>composer install
>```

3. ### Database :
>* set database connection in `.env` file
>```
># DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
>```
>__`Make sure your local server is running`__ and use de command : 
>```
>composer init-db
>```
>It's a shortcut for : 
>```
>php bin/console d:d:d --if-exists --force
>php bin/console d:d:c
>php bin/console d:m:m -n
>```
>_`Demo data will be installed with the last migration file`_

4. ### Jwt authentication
>    * Generate [SSH key](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#installation) for LexikJWTAuthenticationBundle with the command : 
>    ``` 
>    php bin/console lexik:jwt:generate-keypair 
>    ```

5. _(optional)_ Fixtures for test/dev
>    ```
>    composer init-db-dev
>    ```
    
---

## Test Api 
1. Set :  `Content-Type: application/json` in all your request Header
2. Get token : 
    <br>send a `POST` request to `api/login_check` with this `JSON` parameter in the body
    ```
    {
        "username":"SupplierDemo",
        "password":"pwddemo"
    }
    ```
    This will returns you an unique (temporary) token

3. Add token to Header : 
    <br>`Authorization :Bearer token`
---

## Documentation

* Access documentation : send a GET request to : `api/doc.json` 


---
