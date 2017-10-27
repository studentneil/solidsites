<?php
/**
 * Created by PhpStorm.
 * User: neil
 * Date: 27/10/2017
 * Time: 22:28
 */

namespace Solidsites\Controllers;


use Silex\Application;
use Solidsites\Forms\ContactType;
use Symfony\Component\HttpFoundation\Request;


class ContactFormController
{
    public function sendContactFormAction(Request $request, Application $app)
    {
        $initialData = array();
        $form = $app['form.factory']
            ->createBuilder(ContactType::class, $initialData)
            ->getForm();
        $form = $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $transport = (new \Swift_SmtpTransport($app['config']['email']['host'], 587, 'tls'))
                ->setUsername($app['config']['email']['username'])
                ->setPassword($app['config']['email']['password'])
                ->setStreamOptions(array('ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )))
                ->setAuthMode('plain');
            $transport->setLocalDomain('[127.0.0.1]');
            $mailer = new \Swift_Mailer($transport);
            $message = (new \Swift_Message('Solidsites Contact form'))
                ->setFrom(array($app['config']['email']['username'] => 'contact form'))
                ->setTo(array($app['config']['email']['reciever'] => 'neil'))
                ->setReplyTo(array($data['email'] => $data['name']))
                ->setBody($data['message'])
                ->addPart($data['package']);
//          result holds the number of successful recipients
            $result = $mailer->send($message);
            if ($result === 0) {
                $response = "Sorry, but that didnt send. Try again?";
                return $response;
            }else {
                $response = "Thanks! We'll be in touch very soon";
                return $response;
            }


        };

    }

}