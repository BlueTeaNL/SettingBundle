<?php

namespace Bluetea\SettingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', 'choice', [
                'label' => 'bluetea.portal.settings.user.settings.form.locale',
                'choices' => [
                    'nl' => 'bluetea.portal.form.user_settings.locale.nl',
                    'en' => 'bluetea.portal.form.user_settings.locale.en'
                ]
            ])
            ->add('users', 'entity', [
                'label' => 'bluetea.portal.settings.user.settings.form.users',
                'class' => 'BlueteaPortalUserBundle:User',
                'multiple' => true
            ])
            ->add('projects', 'entity', [
                'label' => 'bluetea.portal.settings.user.settings.form.projects',
                'class' => 'BlueteaPortalPlanningBundle:Project',
                'multiple' => true
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'bluetea_portal_settings_user';
    }
}