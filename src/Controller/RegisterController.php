<?php

namespace App\Controller;

use App\Entity\User;

use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegisterController extends AbstractController
{
    private EmailVerifier $emailVerifier;
   
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    /**
     * @Route("/register", name="register",methods={"POST"}) 
     */
    public function register(Request $request,
    UserPasswordEncoderInterface $encoder, UserRepository $userRepository,\Swift_Mailer $mailer): Response
    {    
       
        $parameter = json_decode($request->getContent(),true) ; 
        
        $user = new User();

         $email = $parameter['username'] ;  

         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
             return $this->json([
                 'EmailStructureErrorr' => 'Email not valid' 
             ]) ; 
         }

         $password = $request->get('password') ; 

         $user = $userRepository->findOneBy([
             'email' => $email
         ]) ; 

         if(!is_null($user)){
             return $this->json([
                 'errors' => 'user already exists' 
             ]) ; 
         } 

       
         $user = new User() ; 
         $user->setEmail($email) ; 
       
         $user->setName($parameter['FullName']) ;  
         $user->setTelephoneNumber($parameter['telephoneNumber']) ;
         $user->setCodeSecurite('') ;  
         $password = $parameter['password'] ;
       
         if(strlen($password) < 6 ) 
             {
                 return $this->json([
                     'PasswordError' => 'Password must have at least 6 caracters' 
                 ]) ; 
             } 
       
         $encoded = $encoder->encodePassword($user,  $password );
         $user->setPassword($encoded);  
         $user->setAdresse('  ') ; 
         $user->setBio(' ') ; 
         $user->setMetier($parameter['metier']); 
         $user->setDateAjout(new \DateTime()) ; 
         $user->setRoles(['ROLE_USER']) ; 
         $user->setImage('https://bootdey.com/img/Content/avatar/avatar7.png') ; 
             

         $em = $this->getDoctrine()->getManager() ; 
         $em->persist($user) ;
        
         $em->flush() ; 
       
         $message = (new \Swift_Message(' Pol , Confirm Email'))
                
         ->setFrom('Pol@gmail.com') 
         ->setTo($user->getEmail())
        
         ->setBody(
             $this->renderView(
                 'registration/confirmation_email.html.twig', compact('user')
             ),
             'text/html'
         )
     ;
         $mailer->send($message); 

     
        return $this->json([
            'success' => 'Registered succeffully' 
        ]) ; 
 
    }

       /**
      * @Route("/verify/email", name="app_verify_emaill",methods={"POST"})
      */
     public function verifyUserEmail(Request $request,TokenStorageInterface $tokenStorage, UserRepository $userRepository): Response
     {
        $parameter = json_decode($request->getContent(),true) ; 

        $id = $parameter['id'][1] ; 
        $user = $this->getDoctrine()->getRepository(User::class)->find($id) ;
       
        $user->setIsVerified(1) ; 
         

        
        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($user) ;  
        $em->flush() ;
     
     
         return  $this->json([
             'username'=> $user->getEmail(),
             'password'=> $user->getPassword()
         ]);  
     }





 
}
