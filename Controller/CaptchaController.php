<?php

namespace stz184\CaptchaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CaptchaController extends Controller
{
    public function indexAction()
    {
        $captchaGenerator 	= $this->container->get('stz184_captcha.generator');
        $captchaText 		= mb_substr(uniqid(mt_rand(), true), 0, 6);
        $sessionKey 		= $this->container->getParameter('stz184_captcha.session_key');

        $captchaGenerator->setText($captchaText);
        $this->container->get('session')->set($sessionKey, $captchaText);

        $response = new Response($captchaGenerator->generate());
        $response->headers->set('Content-type', 'image/jpeg');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Cache-Control','no-cache');

        return $response;
    }
}
