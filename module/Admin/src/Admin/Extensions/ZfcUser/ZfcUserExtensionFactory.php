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

class ZfcUserExtensionFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $object = new ZfcUserExtension();
        $object->setUserService($serviceLocator->get('zfcuser_user_service'));
        $object->setFieldset($serviceLocator->get('admin_extension_zfcuser_fieldset'));
        $object->setInputFilter($serviceLocator->get('admin_extension_zfcuser_inputfilter'));

        return $object;
    }
}
