<?php
namespace Application\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

class LoginNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'login';
    }
}