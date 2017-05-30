<?php
/**
 * LdcUserProfile
 *
 * @link      http://github.com/adamlundrigan/LdcUserProfile for the canonical source repository
 * @copyright Copyright (c) 2014 Adam Lundrigan & Contributors
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Extensions\ZfcUser;

use ZfcUser\Form\RegisterFilter;
use ZfcUser\Options\RegistrationOptionsInterface;

class ZfcUserInputFilter extends RegisterFilter
{
    /**
     * Constructor
     *
     * @param Validator\NoOtherRecordExists $emailValidator
     * @param Validator\NoOtherRecordExists $usernameValidator
     */
    public function __construct($emailValidator, $usernameValidator, RegistrationOptionsInterface $options)
    {
        parent::__construct($emailValidator, $usernameValidator, $options);

        $this->add(array(
            'name'       => 'id',
            'required'   => true,
            'filters'    => array(array('name' => 'Digits')),
            'validators' => array(),
        ));

        $this->add(array(
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
        $this->add(array(
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
                    array(
                        'name' => 'Alpha',
                    ),
                ),
            )
        );


        $this->remove('password');
        $this->remove('passwordVerify');

        $this->add(array(
            'name' => 'password',
            'validators' => array(
                array(
                    // What ever your namespace is etc determines this..
                    'name' => 'Admin\Validators\PasswordValidator',
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'passwordVerify',
            'validators' => array(
                array(
                    'name'    => 'Identical',
                    'options' => array(
                        'token' => 'password',
                    ),
                ),
            ),
        ));

        $this->get('password')->setRequired(false);
        $this->get('passwordVerify')->setRequired(false);
    }
}
