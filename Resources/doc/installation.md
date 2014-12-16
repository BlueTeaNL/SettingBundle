Installation
============

## Composer

Install the bundle with composer:

```bash
php composer.phar require "bluetea/setting-bundle" dev-master
```

## Configuration Symfony2

Add the bundle to the AppKernel.php

```php
new \Bluetea\SettingBundle\BlueteaSettingBundle(),
```

## Doctrine Entities

### Extend the Doctrine entities

Create the entities in your bundle.

_src/Acme/SettingBundle/Entity/AppSetting.php_

```php
<?php

namespace Acme\SettingBundle\Entity;

use Bluetea\SettingBundle\Model\AppSetting as BaseAppSetting;

class AppSetting extends BaseAppSetting {

}
```

_src/Acme/SettingBundle/Entity/EntitySetting.php_

```php
<?php

namespace Acme\SettingBundle\Entity;

use Bluetea\SettingBundle\Model\EntitySetting as BaseEntitySetting;

class EntitySetting extends BaseEntitySetting {

}
```

### Enable the Doctrine entities

Add the files _AppSetting.orm.yml_ and _EntitySetting.orm.yml_ to your _Resources/config_ directory.

**AppSetting.orm.yml**

Configure a User entity which implements the UserInterface.

```yml
Acme\SettingBundle\Entity\AppSetting:
    type: entity
    table: setting_app
    id:
        id:
            type: integer
            id: true
            generator:
                strategy: AUTO
    fields:
        app:
            type: string
            length: 255
        setting:
            type: string
            length: 255
        value:
            type: text
            nullable: true
        type:
            type: string
            length: 255
        updatedAt:
            type: datetime
    manyToOne:
        user:
            targetEntity: Acme\UserBundle\Entity\User
    lifecycleCallbacks:
        prePersist: [ updateUpdatedAt ]
        preUpdate: [ updateUpdatedAt ]
```

**EntitySetting.orm.yml**

```yml
Acme\SettingBundle\Entity\EntitySetting:
    type: entity
    table: setting_entity
    id:
        id:
            type: integer
            id: true
            generator:
                strategy: AUTO
    fields:
        entity:
            type: string
            length: 255
        entityId:
            type: integer
            nullable: true
        setting:
            type: string
            length: 255
        value:
            type: text
            nullable: true
        type:
            type: string
            length: 255
        updatedAt:
            type: datetime
    lifecycleCallbacks:
        prePersist: [ updateUpdatedAt ]
        preUpdate: [ updateUpdatedAt ]
```

## Update the database

Check the changes

```bash
php app/console doctrine:schema:update --dump-sql
```

Execute the changes in the database

```bash
php app/console doctrine:schema:update --force
```

Or if you using the Doctrine MigrationsBundle, create a migrations file

```bash
php app/console doctrine:migrations:diff
```

and execute the migrations

```bash
php app/console doctrine:migrations:migrate
```

## Configure the bundle in the _config.yml_

```yaml
bluetea_setting:
    app:
        class: Acme\SettingBundle\Entity\AppSetting
    entity:
        class: Acme\SettingBundle\Entity\EntitySetting
```