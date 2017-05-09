<?php

namespace Admin;

use Zend\ServiceManager\ServiceManager;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mvc\MvcEvent;

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
                'mail.username' => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get('Config');
                    $mail = $config['mail']['transport']['options']['connection_config']['username'];
                    return $mail;
                }
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'zfcuser' => function ($controllerManager) {
                    /* @var ControllerManager $controllerManager */
                    $serviceManager = $controllerManager->getServiceLocator();
                    /* @var RedirectCallback $redirectCallback */
                    $redirectCallback = $serviceManager->get('zfcuser_redirect_callback');
                    /* @var \Admin\Controller\UserController $controller */
                    $controller = new \Admin\Controller\UserController ($redirectCallback);
                    return $controller;
                },
            ),
        );
    }

    /**
     * Extends the ZfcUser registration form with custom fields
     *
     * @param MVCEvent $e
     */
    public function onBootstrap(MVCEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        // custom fields of registration form (ZfcUser)
        $sharedEvents = $eventManager->getSharedManager();
        $sharedEvents->attach('ZfcUser\Form\Register',
            'init',
            function ($e) {
                /* @var $form \ZfcUser\Form\Register */
                $form = $e->getTarget();

                $form->add(
                    array(
                        'name' => 'first_name',
                        'type' => 'text',
                        'options' => array(
                            'label' => 'First name',
                        ),
                    )
                );

                $form->add(
                    array(
                        'name' => 'last_name',
                        'type' => 'text',
                        'options' => array(
                            'label' => 'Last name',
                        ),
                    )
                );

                $form->add(
                    array(
                        'name' => 'logged_in',
                        'type' => 'Zend\Form\Element\Checkbox',
                        'options' => array(
                            'label' => 'Stay logged in',
                            'checked_value' => 1,
                            'unchecked_value' => 0,
                        ),
                    )
                );
            }
        );

        // Validators for custom fields
        $sharedEvents->attach('ZfcUser\Form\RegisterFilter',
            'init',
            function ($e) {
                /* @var $form \ZfcUser\Form\RegisterFilter */
                $filter = $e->getTarget();

                // Custom field firstname
                $filter->add(array(
                        'name' => 'first_name',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'min' => 3,
                                    'max' => 50,
                                ),
                            ),
                            array(
                                'name' => 'Alpha',
                            ),
                        ),
                    )
                );

                // Custom field lastname
                $filter->add(array(
                        'name' => 'last_name',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'min' => 3,
                                    'max' => 50,
                                ),
                            ),
                        ),
                    )
                );

                $filter->add(array(
                    'name' => 'password',
                    'validators' => array(
                        array(
                            // What ever your namespace is etc determines this..
                            'name' => 'Admin\Validators\PasswordValidator',
                        ),
                    ),
                ));
            }
        );
    }
}
