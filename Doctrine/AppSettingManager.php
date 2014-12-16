<?php

namespace Bluetea\SettingBundle\Doctrine;

use Bluetea\SettingBundle\Model\AppSetting;
use Bluetea\SettingBundle\Model\SettingManager;

class AppSettingManager extends SettingManager
{
    /**
     * @param $user
     * @return AppSetting[]
     */
    public function findSettingsByUser($user)
    {
        return $this->findSettingsBy(['user' => $user]);
    }

    /**
     * Update settings
     *
     * @param $settings
     * @param string $app
     * @param null $user
     * @param bool $andFlush
     * @throws \LogicException
     */
    public function updateKeyValueSettings($settings, $app, $user = null, $andFlush = true)
    {
        if (!is_array($settings)) {
            throw new \LogicException(sprintf('Expecting array, getting %s', gettype($settings)));
        }

        if (count($settings) > 0) {
            foreach ($settings as $setting => $value) {
                $this->updateSetting($setting, $value, $app, $user, false);
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
     * @param string $app
     * @param null $user
     * @param bool $andFlush
     */
    public function updateSetting($setting, $value, $app, $user = null, $andFlush = true)
    {
        $criteria = ['setting' => $setting, 'app' => $app];
        if (!is_null($user)) {
            $criteria['user'] = $user;
        }
        /** @var $_setting AppSetting */
        $_setting = $this->findSettingBy($criteria);

        if (is_null($_setting)) {
            $_setting = $this->createSetting();
        }
        $_setting->setSetting($setting);
        $_setting->setApp($app);
        $_setting->setValue($value);
        if (!is_null($user)) {
            $_setting->setUser($user);
        }

        $this->objectManager->persist($_setting);

        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}