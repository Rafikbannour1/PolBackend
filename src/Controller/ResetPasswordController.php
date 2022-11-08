<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twilio\Rest\Client;
class ResetPasswordController extends AbstractController
{
     /**
     * @Route("/reset/password/email", name="resetPassword", methods={"POST"})
     */
    public function resetPassword(Request $request, 
     EntityManagerInterface $entityManager,\Swift_Mailer $mailer,UserRepository $userRepository): Response
    {
      
      $parameter = json_decode($request->getContent(),true) ; 
     
      $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $longueurMax = strlen($caracteres);
      $chaineAleatoire = '';
     
      for ($i = 0; $i < 6; $i++)
      {
      $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
      }
      
       $users = $userRepository->findAll() ; 
       $boolean = false ; 
       $idUser = 0 ; 
       foreach ($users as $user) {
          

           if($user->getEmail() === $parameter['email'])
                {
                    $boolean=true;  
                    $idUser = $user->getId() ; 
                }

       }

       

      if($boolean == true)
      {
        $message = (new \Swift_Message('Configurer votre compte personnel')) 
                            
        ->setFrom('Pol@gmail.tn', 'Pol')  
    
        ->setTo($parameter['email'])
   
        ->setBody(
            $this->renderView(
               'resetPassword/resetpassword.html.twig',compact("chaineAleatoire")
            ),
            'text/html'
        )
        ;
        $mailer->send($message);

        $Userr = $userRepository->find($idUser) ; 
        $Userr->setCodeSecurite($chaineAleatoire) ; 
        
        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($Userr) ; 
        $em->flush() ; 
       
        return $this->json(['resultAfterSend'=>'Email has been sent',
        'result1AfterSend'=>'Please check your inbox for the security code. Remember to look in your spam folder.
        ']) ; 

      }

      else {
        return $this->json(['error'=>'Check your email ! ',]) ; 
      }


     
    
    
    
        
    }

    /**
     * @Route("/reset/password/telephone", name="resetPasswordTel", methods={"POST"})
     */
    public function resetPasswordTel(Request $request, 
     EntityManagerInterface $entityManager,\Swift_Mailer $mailer,UserRepository $userRepository): Response
    {
      
      $parameter = json_decode($request->getContent(),true) ; 
     
      $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $longueurMax = strlen($caracteres);
      $chaineAleatoire = '';
     
      for ($i = 0; $i < 6; $i++)
      {
      $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
      }
      
       $users = $userRepository->findAll() ; 
       $boolean = false ; 
       $idUser = 0 ; 
      
       foreach ($users as $user) {
          

           if($user->getTelephoneNumber() == $parameter['number']) 
                {
                    $boolean=true;  
                    $idUser = $user->getId() ; 
                }
                
       }

  

      if($boolean == true)
      {
        $sid    = "AC27324bec140e6ba899b305613169b357"; 
        $token  = "5c371ab33aec30e8622ef0cc206a611a"; 
        $twilio = new Client($sid, $token); 
        $numero = '+216'.$parameter['number']  ; 
            
                $message = $twilio->messages 
                ->create($numero, // to 
                        array(  
                           
                            "messagingServiceSid" => "MGf73dd7f6d03753562915d8fb17346792",      
                            "body" => "Your code to reset password is " .$chaineAleatoire
                        ) 
                );
   
      

        $Userr = $userRepository->find($idUser) ; 
        $Userr->setCodeSecurite($chaineAleatoire) ; 
        
        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($Userr) ; 
        $em->flush() ; 
       
        return $this->json(['resultAfterSend'=>'Code has been sent',
        'result1AfterSend'=>'Please check your inbox for the security code. Remember to look in your spam folder.
        ']) ; 

      }

      else {
        return $this->json(['error'=>'Check your telephone number ! ',]) ; 
      }


     
    
    
    
        
    }





    /**
     * @Route("/reset/password/checkCode", name="checkCode", methods={"POST"})
     */
    public function checkCode(Request $request, 
     EntityManagerInterface $entityManager,\Swift_Mailer $mailer,UserRepository $userRepository): Response
    {

        $parameter = json_decode($request->getContent(),true) ; 
       
        $user = $userRepository->findOneBy ([
            "email" => $parameter['email']
            
        ]) ; 

        $code = $parameter['code'] ; 
        
          

        if($user->getCodeSecurite() == $code)
        {
            return $this->json(['success'=>'success']) ; 
        }
        else {
            return $this->json(['failure'=>'failure']) ; 
        }




    }

    
    /**
     * @Route("/reset/password/checkCodeTelephone", name="checkCodeTelephone", methods={"POST"})
     */
    public function checkCodeTelephone(Request $request, 
     EntityManagerInterface $entityManager,\Swift_Mailer $mailer,UserRepository $userRepository): Response
    {

        $parameter = json_decode($request->getContent(),true) ; 
       
        $user = $userRepository->findOneBy ([
            "telephoneNumber" => $parameter['number']
            
        ]) ; 

        $code = $parameter['code'] ; 
        
          

        if($user->getCodeSecurite() == $code)
        {
            return $this->json(['success'=>'success']) ; 
        }
        else {
            return $this->json(['failure'=>'failure']) ; 
        }




    }


 /**
     * @Route("/reset/password/changePassword", name="changePass", methods={"PUT"})
     */
    public function changePass(Request $request, 
     EntityManagerInterface $entityManager,UserPasswordEncoderInterface $encoder,UserRepository $userRepository): Response
    {
        $parameter = json_decode($request->getContent(),true) ; 
       
        $newPassword = $parameter['newPassword'] ; 


        $user = $userRepository->findOneBy ([
            "email" => $parameter['email']
            
        ]) ; 

        $encoded = $encoder->encodePassword($user, $newPassword);
        
        $user->setPassword($encoded); 
        $user->setCodeSecurite('') ;      
        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($user) ; 
        $em->flush() ; 



        return $this->json('Password updated succefully') ; 
    }



     /**
     * @Route("/reset/password/changePasswordTelephone", name="changePassTelp", methods={"PUT"})
     */
    public function changePassTelp(Request $request, 
     EntityManagerInterface $entityManager,UserPasswordEncoderInterface $encoder,UserRepository $userRepository): Response
    {
        $parameter = json_decode($request->getContent(),true) ; 
       
        $newPassword = $parameter['newPassword'] ; 


        $user = $userRepository->findOneBy ([
            "telephoneNumber" => $parameter['number']
            
        ]) ; 

      if(empty($newPassword))
        {
          return $this->json([
            'PasswordError' => 'Please input your password' 
        ]) ; 
        }    


       else if(strlen($newPassword) < 6 ) 
        {
            return $this->json([
                'PasswordError' => 'Password must have at least 6 caracters' 
            ]) ; 
        } 
        
        else

        $encoded = $encoder->encodePassword($user, $newPassword);
        
        $user->setPassword($encoded); 
        $user->setCodeSecurite('') ;      
        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($user) ; 
        $em->flush() ; 



        return $this->json('Password updated succefully') ; 
    }

}
