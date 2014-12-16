<?php

namespace Bluetea\SettingBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * AppSetting
 */
abstract class AppSetting extends Setting
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $app;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set app
     *
     * @param string $app
     * @return AppSetting
     */
    public function setApp($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get app
     *
     * @return string
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}