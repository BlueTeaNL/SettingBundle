<?php

namespace Bluetea\SettingBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;

class SettingManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     * @param $class
     */
    public function __construct(ObjectManager $objectManager, $class)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;

        $this->repository = $this->objectManager->getRepository($this->class);
    }

    /**
     * @param Setting[] $settings
     * @return array
     */
    public function convertSettingsToKeyValue($settings)
    {
        $_settings = [];
        foreach ($settings as $setting) {
            $_settings[$setting->getSetting()] = $setting->getValue();
        }
        return $_settings;
    }

    /**
     * @return Setting
     */
    public function createSetting()
    {
        $class = new $this->class;
        return $class;
    }

    /**
     * @param $criteria
     * @return Setting[]
     */
    public function findSettingsBy($criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * @param $criteria
     * @return Setting
     */
    public function findSettingBy($criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param $id
     * @return Setting
     */
    public function findSetting($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}