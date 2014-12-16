<?php

namespace Bluetea\SettingBundle\EventListener;

use Bluetea\SettingBundle\Exception\InvalidSettingException;
use Bluetea\SettingBundle\Model\Setting;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\User\UserInterface;

class DoctrineListener
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @param Container $container
     */
    function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $em = $args->getObjectManager();
        $entity = $args->getObject();

        if ($entity instanceof Setting) {
            switch ($entity->getType()) {
                case Setting::TYPE_ENTITY:
                    $unserializedValue = unserialize($entity->getValue());
                    if (!$this->isEntityArray($unserializedValue)) {
                        $value = $em->getRepository($unserializedValue['class'])->find($unserializedValue['id']);
                    } else {
                        $value = new ArrayCollection();
                        foreach ($unserializedValue as $row) {
                            $value->add($em->getRepository($row['class'])->find($row['id']));
                        }
                    }
                    break;
                case Setting::TYPE_OBJECT:
                case Setting::TYPE_DATETIME:
                case Setting::TYPE_ARRAY:
                    $value = unserialize($entity->getValue());
                    break;
                case Setting::TYPE_BOOLEAN:
                    $value = ($entity->getValue() == 1) ? true : false;
                    break;
                case Setting::TYPE_INTEGER:
                    $value = (int)$entity->getValue();
                    break;
                case Setting::TYPE_FLOAT:
                    $value = (float)$entity->getValue();
                    break;
                case Setting::TYPE_STRING:
                    $value = (string)$entity->getValue();
                    break;
                default:
                    $value = $entity->getValue();
                    break;
            }
            $entity->setValue($value);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\DBAL\DBALException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Setting) {
            $entity->setType($this->determineType($entity->getValue()));
            switch ($entity->getType()) {
                case Setting::TYPE_ENTITY:
                    if (!$this->isEntityArray($entity->getValue())) {
                        if (!method_exists($entity->getValue(), 'getId')) {
                            throw new InvalidSettingException(
                                sprintf(
                                    'Setting \'%s\' has an entity type but the value isn\'t an entity',
                                    $entity->getSetting()
                                )
                            );
                        }
                        $value = serialize([
                            'class' => ClassUtils::getRealClass(get_class($entity->getValue())),
                            'id' => $entity->getValue()->getId()
                        ]);
                    } else {
                        $value = array();
                        foreach ($entity->getValue() as $row) {
                            if (!method_exists($row, 'getId')) {
                                throw new InvalidSettingException(
                                    sprintf(
                                        'Setting \'%s\' has an entity type but the value isn\'t an entity',
                                        $entity->getSetting()
                                    )
                                );
                            }
                            $value[] = [
                                'class' => ClassUtils::getRealClass(get_class($row)),
                                'id' => $row->getId()
                            ];
                        }
                        $value = serialize($value);
                    }
                    break;
                case Setting::TYPE_OBJECT:
                case Setting::TYPE_DATETIME:
                case Setting::TYPE_ARRAY:
                    $value = serialize($entity->getValue());
                    break;
                case Setting::TYPE_BOOLEAN:
                    $value = ($entity->getValue()) ? 1 : 0;
                    break;
                case Setting::TYPE_INTEGER:
                    $value = (int)$entity->getValue();
                    break;
                case Setting::TYPE_FLOAT:
                    $value = (float)$entity->getValue();
                    break;
                case Setting::TYPE_STRING:
                    $value = (string)$entity->getValue();
                    break;
                default:
                    $value = $entity->getValue();
                    break;
            }
            $entity->setValue($value);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->postLoad($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\DBAL\DBALException
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->prePersist($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->postLoad($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\DBAL\DBALException
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $em = $args->getObjectManager();
        $entity = $args->getObject();

        // Check if the entity is a User
        if ($entity instanceof UserInterface) {
            // Check if there are settings from a user which should be deleted if the user is deleted
            $settings = $this->container->get('bluetea_setting.app_setting_manager')->findSettingsByUser($entity);
            if (count($settings) > 0) {
                foreach ($settings as $setting) {
                    // Remove the setting because the user won't exist anymore
                    $em->remove($setting);
                }
            }
        }

        // Check if the entity is part of a setting
        $settings = $this->container->get('bluetea_setting.entity_setting_manager')->findSettingsByEntity($entity);
        if (count($settings) > 0) {
            foreach ($settings as $setting) {
                // Remove the setting because the entity doesn't exist anymore
                $em->remove($setting);
            }
        }
    }

    /**
     * Check if a given value is an entity array
     *
     * @param $value
     * @return bool
     */
    protected function isEntityArray($value)
    {
        if ($value instanceof ArrayCollection) {
            return true;
        } elseif (is_array($value)) {
            // Check if all arrays are entity arrays, if not return false
            foreach ($value as $row) {
                if (!isset($row['class']) || !isset($row['id'])) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Determine the type of the value. Default string.
     *
     * @param $value
     * @return string
     */
    protected function determineType($value)
    {
        switch ($type = gettype($value)) {
            case "boolean":
            case "string":
            case "integer":
            case "array":
                return $type;
                break;
            case "double":
                return Setting::TYPE_FLOAT;
                break;
            case "object":
                if ($value instanceof \DateTime) {
                    return Setting::TYPE_DATETIME;
                } else if (method_exists($value, 'getId')) {
                    return Setting::TYPE_ENTITY;
                } else if ($value instanceof ArrayCollection) {
                    return Setting::TYPE_ENTITY;
                } else {
                    return Setting::TYPE_OBJECT;
                }
                break;
            default:
                return Setting::TYPE_STRING;
                break;
        }
    }
}