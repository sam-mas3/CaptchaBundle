<?php

namespace stz184\CaptchaBundle\Validator;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CaptchaValidator {

    /** @var SessionInterface  */
    protected $session;
    /** @var TranslatorInterface */
    protected $translator;
    /** @var string */
    protected $sessionKey;
    protected $invalidMessage;

    public function __construct(SessionInterface $session, TranslatorInterface $translator, $sessionKey, $invalidMessage)
    {
        $this->session 			= $session;
        $this->translator 		= $translator;
        $this->sessionKey		= $sessionKey;
        $this->invalidMessage	= $invalidMessage;
    }

    protected function getExpectedCode()
    {
        return $this->session->get($this->sessionKey);
    }

    public function validate(FormEvent $event)
    {
        $form			= $event->getForm();
        $submittedCode	= $event->getData();
        $expectedCode 	= $this->getExpectedCode();

        if (!($submittedCode && is_string($submittedCode) && mb_strtolower($submittedCode) == mb_strtolower($expectedCode))) {
            $form->addError(
                new FormError($this->translator->trans($this->invalidMessage . '('.$expectedCode.')', array(), 'validators'))
            );
        }
    }
} 