<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\Groupe;
use Stripe\PaymentIntent;
use App\Entity\HistoriqueAchat;
use App\Repository\UserRepository;
use App\Repository\GroupeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PaymentController extends AbstractController
{
    /**
     * @Route("/pol/payment", name="payment",methods={"POST"})
     */
    public function Pay(Request $request,TokenStorageInterface $tokenStorage,
    GroupeRepository $courRepository): Response
    {
        $parameter = json_decode($request->getContent(),true) ; 
        
        $StripeToken = $parameter['token'] ; 

        \Stripe\Stripe::setApiKey('sk_test_51KrIeWLtyqgzrQY7A8apNLm1ZYVN7otNlHNaJyxMIEbwHjWOQ4Z2qYjEifGzgMnkPdkZo2TuvabwnOG51C4eE8xB00B6rhC4Aw') ;
        \Stripe\Charge::create(array(
            "amount"=>$parameter['price'],
            "currency"=>"eur",
            "source" =>$StripeToken,
            "description"=>"Payment Succefully"
        ));
      
        $parameter = json_decode($request->getContent(),true) ; 
        
        $user = $tokenStorage->getToken()->getUser() ; 
        
        $courses = $parameter['courses'] ;   
         
    
      
        foreach($courses as $cour)
          {
              
            $user->addCour($courRepository->find($cour['id'])) ; 
            $History = new HistoriqueAchat  ; 
                
            $History->setDateAchat(new \DateTime('now')) ;     
            $History->setCours($courRepository->find($cour['id'])) ;  
            $History->setUser($user) ; 
            
           
           
            $em = $this->getDoctrine()->getManager() ;  
            $em->persist($History) ;
            $em->flush() ;
           
          }  
        

         $em = $this->getDoctrine()->getManager() ;  
         $em->persist($user) ;
         $em->flush() ;
  


        return $this->json([
            'success'=>'Payment Succefully'
        ]) ; 
    }


  



}
