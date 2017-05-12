<?php
/**
 * LdcUserProfile
 *
 * @link      http://github.com/adamlundrigan/LdcUserProfile for the canonical source repository
 * @copyright Copyright (c) 2014 Adam Lundrigan & Contributors
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Extensions\ZfcUser;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Admin\Form\AdminUserForm as AdminUserForm;

class ZfcUserFieldsetFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('zfcuser_module_options');
        $entityClass = $options->getUserEntityClass();

        $object = new ZfcUserFieldset(new AdminUserForm($options));

        $object->setHydrator($serviceLocator->get('zfcuser_user_hydrator'));
        $object->setObject(new $entityClass());

        return $object;
    }
}
