<?php
namespace Admin;

use Zend\ServiceManager\ServiceManager;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'mail.transport' => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get('Config');
                    $transport = new SmtpTransport();
                    $transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));

                    return $transport;
                },
            ),
        );
    }
}
