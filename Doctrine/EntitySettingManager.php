<?php

namespace Bluetea\SettingBundle\Doctrine;

use Bluetea\SettingBundle\Model\EntitySetting;
use Bluetea\SettingBundle\Model\SettingManager;

class EntitySettingManager extends SettingManager
{
    /**
     * @param $entity
     * @param $entityId
     * @throws \LogicException
     * @return EntitySetting[]
     */
    public function findSettingsByEntity($entity, $entityId = null)
    {
        if (is_null($entityId)) {
            if (!is_object($entity) || !method_exists($entity, 'getId')) {
                throw new \LogicException('Given entity isn\'t an actual entity and no ID is given');
            }
            $entityId = $entity->getId();
            $entity = $this->objectManager->getClassMetadata(get_class($entity))->getName();
        }

        return $this->repository->findBy(['entity' => $entity, 'entityId' => $entityId]);
    }

    /**
     * Update settings
     *
     * @param $settings
     * @param string $entity
     * @param null $entityId
     * @param bool $andFlush
     * @throws \LogicException
     */
    public function updateKeyValueSettings($settings, $entity, $entityId = null, $andFlush = true)
    {
        if (!is_array($settings)) {
            throw new \LogicException(sprintf('Expecting array, getting %s', gettype($settings)));
        }

        if (count($settings) > 0) {
            foreach ($settings as $setting => $value) {
                $this->updateSetting($setting, $value, $entity, $entityId, false);
            }
            if ($andFlush) {
                $this->objectManager->flush();
            }
        }
    }

    /**
     * Update setting
     *
     * @param $setting
     * @param $value
     * @param string $entity
     * @param int $entityId
     * @param bool $andFlush
     */
    public function updateSetting($setting, $value, $entity, $entityId = null, $andFlush = true)
    {
        if (is_object($entity)) {
            if (method_exists($entity, 'getId')) {
                $entityId = $entity->getId();
            }
            $entity = $this->objectManager->getClassMetadata(get_class($entity))->getName();
        }

        $criteria = ['setting' => $setting, 'entity' => $entity, 'entityId' => $entityId];
        /** @var $_setting EntitySetting */
        $_setting = $this->findSettingBy($criteria);

        if (is_null($_setting)) {
            $_setting = $this->createSetting();
        }
        $_setting->setSetting($setting);
        $_setting->setEntity($entity);
        $_setting->setEntityId($entityId);
        $_setting->setValue($value);

        $this->objectManager->persist($_setting);

        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}