<?php

namespace Admin\Form;

use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use ZfcUser\Form\Base as ZfcUserBaseForm;

class AdminUserForm extends ZfcUserBaseForm
{
    public function __construct()
    {
        parent::__construct();

        $this->add(array(
            'name' => 'first_name',
            'options' => array(
                'label' => 'First Name',
            ),
            'attributes' => array(
                'type' => 'text'
            ),
        ));

        $this->add(array(
            'name' => 'last_name',
            'options' => array(
                'label' => 'Last Name',
            ),
            'attributes' => array(
                'type' => 'text'
            ),
        ));

        // @TODO: Fix this... getValidator() is a protected method.
        //$csrf = new Element\Csrf('csrf');
        //$csrf->getValidator()->setTimeout($this->getRegistrationOptions()->getUserFormTimeout());
        //$this->add($csrf);

        $this->getEventManager()->trigger('init', $this);
    }

    public function init()
    {
    }
}
