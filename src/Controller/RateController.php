<?php

namespace App\Controller;

use App\Entity\Rate;
use App\Entity\Groupe;
use App\Repository\RateRepository;
use App\Repository\GroupeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RateController extends AbstractController
{
    

    /**
     * @Route("/pol/rate/{idCours}/{rateValue}", name="RATEPUT", methods={"POST"}) 
     */
    public function RATEPUT(Request $request,
    GroupeRepository $groupeRepository,$idCours,$rateValue ,
    TokenStorageInterface $tokenStorage,RateRepository $rateRepository): Response
    {
        $cour = $this->getDoctrine()->getRepository(Groupe::class)->find($idCours) ; 
        $rate = new Rate() ; 
        $user = $tokenStorage->getToken()->getUser()  ; 
        
        $rates = $rateRepository->findAll() ; 

        $rateId = 0 ; 
        $bolean = false ; 
     

        foreach($rates as $r)
            {
               
                         if(($r->getUser() == $user) && ($r->getCours()->getId() == $idCours))
                                {
                                    $bolean=true ; 
                                    $rateId = $r->getId() ; 
                                }
                           
             
            }
      
        
            if($bolean==true)
            {
            $Rate = $this->getDoctrine()->getRepository(Rate::class)->find($rateId) ; 
            $Rate->setCours($cour) ; 
            $Rate->setUser($user) ; 
            $Rate->setMessage('') ; 
            $Rate->setRateValue($rateValue); 
           
            $em = $this->getDoctrine()->getManager() ; 
            $em->persist($Rate)  ; 
            $em->flush() ; 
            }
           
           else{
            $rate->setCours($cour) ; 
            $rate->setUser($user) ; 
            $rate->setRateValue($rateValue); 
            $rate->setMessage('') ; 
            $em = $this->getDoctrine()->getManager() ; 
            $em->persist($rate)  ; 
            $em->flush() ; 
           }
          
           
           $courRates = ($cour->getRates());   
          
           $ratesArray = [] ; 
          
           foreach ($courRates as $rate)
               {
                  $ratesArray [] = $rate->getRateValue(); 
   
               }
   
               $FinalRate = 0 ; 
               $count = 0 ; 
               
           foreach($ratesArray as $oneRate)
               {
                   $count += $oneRate ; 
                   
               }
   
               $FinalRate = $count/count($courRates) ;  
                $cour->setRateValue($FinalRate) ; 
                $em = $this->getDoctrine()->getManager() ; 
                $em->persist($cour)  ; 
                $em->flush() ; 

      
        return $this->json(' Post Rate '); 
        
    
    }

    /**
     * @Route("/pol/getRate/{idCours}", name="getRates", methods={"GET"}) 
     */
    public function getRates(Request $request,
    GroupeRepository $groupeRepository,$idCours,
    TokenStorageInterface $tokenStorage,RateRepository $rateRepository): Response
    {
        $cour = $this->getDoctrine()->getRepository(Groupe::class)->find($idCours) ; 

        $courRates = ($cour->getRates());   
        
        $ratesArray = [] ; 
        foreach ($courRates as $rate)
            {
               $ratesArray [] = $rate->getRateValue(); 

            }

            $FinalRate = 0 ; 
            $count = 0 ; 
            foreach($ratesArray as $oneRate)
            {
                $count += $oneRate ; 
                
            }

            $FinalRate = $count/count($courRates) ; 
        
            return $this->json($FinalRate) ; 
    
    }

 /**
     * @Route("/pol/getYourRate/{idCours}", name="getYourRate", methods={"GET"}) 
     */
    public function getYourRate(Request $request,
    GroupeRepository $groupeRepository,$idCours,
    TokenStorageInterface $tokenStorage,RateRepository $rateRepository): Response
    {
        $user = $tokenStorage->getToken()->getUser() ; 
        $rates = $rateRepository->findAll() ; 
        $myRates = 0 ; 
        



        foreach($rates as $rate)
        {
            $bolean = false ;   
            if(($rate->getUser() == $user ) && ($rate->getCours()->getId() == $idCours))
                {
                        $myRates = $rate->getRateValue() ; 
                       $bolean = true  ; 
                }
        }

       
    
    return $this->json($myRates) ; 
    
    
    }


     /**
     * @Route("/pol/describe/opinion/{idCours}", name="describe", methods={"POST"}) 
     */
    public function description(Request $request,
    GroupeRepository $groupeRepository,$idCours,
    TokenStorageInterface $tokenStorage,RateRepository $rateRepository): Response
    {
        $parameter = json_decode($request->getContent(),true) ; 
        $cour = $this->getDoctrine()->getRepository(Groupe::class)->find($idCours) ; 
        $rate = new Rate() ; 
        $user = $tokenStorage->getToken()->getUser()  ; 
        
        $rates = $rateRepository->findAll() ; 

        $rateId = 0 ; 
        $bolean = false ; 
     

        foreach($rates as $r)
            {
               
                         if(($r->getUser() == $user) && ($r->getCours()->getId() == $idCours))
                                {
                                    $bolean=true ; 
                                    $rateId = $r->getId() ; 
                                }
                           
             
            }
      
        
            if($bolean==true)
            {
            $Rate = $this->getDoctrine()->getRepository(Rate::class)->find($rateId) ; 
            
            $Rate->setMessage($parameter['message']) ; 
      
           
            $em = $this->getDoctrine()->getManager() ; 
            $em->persist($Rate)  ; 
            $em->flush() ; 
            }
           
           else{
         
       
            $rate->setMessage($parameter['message']) ; 
            $em = $this->getDoctrine()->getManager() ; 
            $em->persist($rate)  ; 
            $em->flush() ; 
           }
          
           
           

      
        return $this->json('  Message Sent Succefully');  
    
    
    }


    /**
     * @Route("/pol/MyMessages/{coursId}", name="MyMessages", methods={"GET"}) 
     */
    public function MyMessages(Request $request,
    GroupeRepository $groupeRepository,
    TokenStorageInterface $tokenStorage,RateRepository $rateRepository,$coursId): Response
    {
        $user = $tokenStorage->getToken()->getUser() ; 
        $cour = $this->getDoctrine()->getRepository(Groupe::class)->find($coursId) ; 
        $rates = $rateRepository->findAll() ; 
        $myMessages  = []   ; 




        foreach($rates as $rate)
        {
           if(($rate->getCours()->getUser() == $user) && ($cour == $rate->getCours()) )
            { 
                if(!empty($rate->getMessage()))
                $myMessages [] = $rate->toArray() ; 
            }
        }

       
    
    return $this->json($myMessages) ; 
    
    
    }





}
