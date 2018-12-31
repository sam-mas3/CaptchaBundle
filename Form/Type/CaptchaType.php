<?php

namespace stz184\CaptchaBundle\Form\Type;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CaptchaType extends AbstractType
{
	/** @var  SessionInterface */
	protected $session;
	/** @var  TranslatorInterface */
	protected $translator;
	/** @var  string */
	protected $sessionKey;
	protected $width;
	protected $height;
	protected $options;

	public function __construct(SessionInterface $sessionInterface, TranslatorInterface $translatorInterface, array $config)
	{
		$this->session 		= $sessionInterface;
		$this->translator 	= $translatorInterface;
		$this->sessionKey	= $config['session_key'];
		$this->width		= $config['width'];
		$this->height		= $config['height'];
	}

	/**
	 * Returns the name of this type.
	 *
	 * @return string The name of this type
	 */
	public function getName()
	{
		return 'captcha';
	}

	public function getParent()
	{
		return 'text';
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$this->options['mapped'] = false;
		$resolver->setDefaults($this->options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars = array_merge($view->vars, array(
			'value'     		=> '',
			'image_id'		=> uniqid('captcha_'),
			'captcha_width' 	=> $this->width,
			'captcha_height' 	=> $this->height,
			'reload' 		=> true
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$expectedCode = $this->session->get($this->sessionKey);
		$errorMessage = $this->translator->trans('You have submitted an invalid security code', array(), 'captcha');
		$builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($expectedCode, $errorMessage) {
			$form		= $event->getForm();
			$submittedCode	= $event->getData();

			if (!($submittedCode && is_string($submittedCode) && mb_strtolower($submittedCode) == mb_strtolower($expectedCode))) {
				$form->addError(new FormError($errorMessage));
			}
		});
	}
}
