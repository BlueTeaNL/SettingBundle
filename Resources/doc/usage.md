Settings
========

The SettingBundle allows us to use settings in all applications and for all entities.

## App settings

Use the app settings manager to add and update settings for apps.

```
$settingManager = $this->get('bluetea_settings.app_setting_manager');
// Find a setting
$setting = $settingManager->findSettingBy(['setting' => 'view']);
// Update a setting
$settingManager->updateSetting('view', 'full', 'my_app');
```

## Entity settings

Use the entity settings manager to add and update setting for entities.

```
// Get entity
$entity = $this->getDoctrine()->getRepository('AcmeCoreBundle:Entity')
    ->findOneByReference('00001');
$settingManager = $this->get('bluetea_settings.entity_setting_manager');
// Find settings on Entity
$entitySettings = $settingManager->findSettingsByEntity($entity);
// Update a setting for the Department entity
$settingManager->updateSetting('start_time', new \DateTime('09:00'), $entity);
```

## Bulk action

It's also possible to set an array of settings:

```
$settingManager->updateKeyValueSettings(
    ['start_time' => new \DateTime('09:00'), 'end_time' => new \DateTime('18:00')],
    $entity
);
```

## Persist and Flush

The update methods are persisting and flushing by default. It's possible to disable automatic flushing by setting
the `$andFlush` parameter to `false`.