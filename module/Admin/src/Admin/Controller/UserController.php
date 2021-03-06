<?php

namespace Admin\Controller;

use ZfcUser\Controller\UserController as AdminUserController;
use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;
use ZfcUser\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;
use Zend\Mail;

class UserController extends AdminUserController
{
    /**
     * Register new user
     */
    public function registerAction()
    {
        // if the user is logged in, we don't need to register
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }
        // if registration is disabled
        if (!$this->getOptions()->getEnableRegistration()) {
            return array('enableRegistration' => false);
        }

        $request = $this->getRequest();
        $service = $this->getUserService();
        $form = $this->getRegisterForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        $redirectUrl = $this->url()->fromRoute(static::ROUTE_REGISTER)
            . ($redirect ? '?redirect=' . rawurlencode($redirect) : '');
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'registerForm' => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect' => $redirect,
            );
        }

        $post = $prg;
        $user = $service->register($post);

        $redirect = isset($prg['redirect']) ? $prg['redirect'] : null;

        if (!$user) {
            return array(
                'registerForm' => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect' => $redirect,
            );
        }

        $user->setConfirmationCode($this->generateConfirmationCode());
        $service->getUserMapper()->update($user);

        $this->sendConfirmationEmail($user);
        $this->flashMessenger()->addSuccessMessage('Your information has been sent successfully. In order to complete your registration, please click the confirmation link in the email that we have sent to you.');

        if ($service->getOptions()->getLoginAfterRegistration()) {
            $identityFields = $service->getOptions()->getAuthIdentityFields();
            if (in_array('email', $identityFields)) {
                $post['identity'] = $user->getEmail();
            } elseif (in_array('username', $identityFields)) {
                $post['identity'] = $user->getUsername();
            }
            $post['credential'] = $post['password'];
            $request->setPost(new Parameters($post));
            return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));
        }

        // TODO: Add the redirect parameter here...
        return $this->redirect()->toUrl($this->url()->fromRoute(static::ROUTE_LOGIN) . ($redirect ? '?redirect=' . rawurlencode($redirect) : ''));
    }

    /**
     * Register new user
     */
    public function confirmAction()
    {
        $status = false;

        $user_id = $this->getEvent()->getRouteMatch()->getParam('user_id');
        $code = $this->getEvent()->getRouteMatch()->getParam('code');

        $service = $this->getUserService();
        $user = $service->getUserMapper()->findById($user_id);

        if (!$user) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        if ($code == $user->getConfirmationCode()) {
            $user->setState(1);
            $service->getUserMapper()->update($user);
            $status = true;
        }
        return array(
            'status' => $status,
        );
    }

    /**
     * Send confirmation email.
     *
     * @param $user
     * @return $this
     */
    public function sendConfirmationEmail($user)
    {
        $transport = $this->getServiceLocator()->get('mail.transport');
        $from = $this->getServiceLocator()->get('mail.username');

        $url = $this->url()->fromRoute(
            'zfcuser/confirm',
            array(
                'user_id' => $user->getId(),
                'code' => $user->getConfirmationCode()
            ),
            array('force_canonical' => true)
        );

        $mail = new Mail\Message();
        $mail->setFrom($from);
        $mail->addTo($user->getEmail(), $user->getFirstName());
        $mail->setSubject('Follow the link to confirm your email address');
        $mail->setBody($url);

        $transport->send($mail);
    }

    /**
     * generate confirmation code
     *
     * @param $length - the length of the code.
     * @return $code
     */
    public function generateConfirmationCode($length = 50)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
