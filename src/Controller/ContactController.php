<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact" ,methods={"POST"})
     */
    public function index(Request $request,\Swift_Mailer $mailer)
    {
        // $form = $this->createForm(ContactType::class);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
           

            $contact = json_decode($request->getContent(),true) ;

            if (!filter_var($contact['email'], FILTER_VALIDATE_EMAIL) && !(empty($contact['email'])) ) {
                return $this->json([
                    'EmailStructureErrorr' => 'Email does not exist' 
                ]) ; 
            }
           
            // On crée le message
           
            $message = (new \Swift_Message('Nouveau contact'))
                
                ->setFrom($contact['email'])  
             
                ->setTo('rafik.bannour99@gmail.com')
               
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig', compact('contact')
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);

            return $this->json("envoyeeee") ; 
        }
    //     return $this->render('contact/index.html.twig',['contactForm' => $form->createView()]);
    // } 



}
