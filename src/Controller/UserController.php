<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twilio\Rest\Client;

  


/**
 * @Route("/pol/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
       
        $users =   $userRepository->findAll() ;  
        
        $userArray = [] ; 

        foreach($users as $user)
         {
 
             $roles=$user->getRoles();   
                
         if(in_array("ROLE_USER",$roles)) 
              
            $userArray[]=$user->toArray() ; 
               
         }
 
         return $this->json($userArray) ; 


    }

    /**
     * @Route("/admin", name="user_admin", methods={"GET"})
     */
    public function admin(UserRepository $userRepository): Response
    {
       
        $Admins =   $userRepository->findAll() ;  
        
        $AdminArray = [] ; 

        foreach($Admins as $Admin)
         {
 
             $roles=$Admin->getRoles();   
                
             if(in_array("ROLE_ADMIN",$roles)) 
              
            $AdminArray[]=$Admin->toArray() ; 
               
         }
 
         return $this->json($AdminArray) ; 


    }




     /**
      * @Route("/currentUser", name="user", methods={"GET"})
      */
     public function user(TokenStorageInterface $tokenStorage): Response
     {
         $user = $tokenStorage->getToken()->getUser();  
         return $this->json($user->toArray()) ;   
     }


     /**
     * @Route("/show/{id}", name="user_showUser", methods={"GET"})
     */
    public function showUser(Request $request, User $user, EntityManagerInterface $entityManager,$id
    ,TokenStorageInterface $tokenStorage): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id) ;  
        
        return $this->json($user->toArray());
    }

    /**
     * @Route("/edit/{id}", name="user_edit", methods={"PUT"})
     */
    public function editInfo(Request $request, User $user, 
    EntityManagerInterface $entityManager,UserPasswordEncoderInterface $encoder,TokenStorageInterface $tokenStorage,$id): Response
    {
        $data = $this->getDoctrine()->getRepository(User::class)->find($id) ; 
        $parameter = json_decode($request->getContent(),true) ; 
        
         
          //$encoded = $encoder->encodePassword($data, $parameter['password']);
        
        $data->setEmail($parameter['email']) ; 
        //   $password = $tokenStorage->getToken()->getUser()->getPassword();  
        //   $data->setPassword($password); 
         $data->setBio($parameter['bio']) ;  
         $data->setName($parameter['name']) ;  
         $data->setMetier($parameter['metier']) ;  
         $data->setAdresse($parameter['adresse']) ;  
        //   $roles = $tokenStorage->getToken()->getUser()->getRoles() ; 
        //   if(in_array("ROLE_USER",$roles)) 
        //   {  
        //   $data->setRoles(['ROLE_USER']) ; 
        //   }
        //   else if (in_array("ROLE_ADMIN",$roles))
        //   {
        //      $data->setRoles(['ROLE_ADMIN']) ; 
        //   }
        //   else if (in_array("ROLE_EDITOR",$roles))
        //   {
        //      $data->setRoles(['ROLE_EDITOR']) ; 
        //   }
         
        //   $data->setImage($tokenStorage->getToken()->getUser()->getImage()) ; 
        //   $data->setDateAjout(new \DateTime()) ; 

        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($data) ; 
       
        $em->flush() ; 

        return $this->json('Updated successfully') ; 
    }


     /**
     * @Route("/editPassword", name="user_edit_password", methods={"PUT"}) 
     */
    public function editPassword(Request $request, 
    EntityManagerInterface $entityManager,
    UserPasswordEncoderInterface $encoder,TokenStorageInterface $tokenStorage): Response
    {
        $user = $tokenStorage->getToken()->getUser() ; 
       
         $parameter = json_decode($request->getContent(),true) ; 
        
         $enteredPassword = $parameter['password'] ; 
        
         $currentPassword =  $parameter['currentPassword'] ;
         
         $checkPass = $encoder->isPasswordValid($user, $currentPassword);
         
         if($checkPass === true) {
            
         if(strlen($enteredPassword) < 6)
         {
             return $this->json(['enteredPasswordError'=> 'Password should have at least 6 caracters']) ; 
         }
         else {
            $encoded = $encoder->encodePassword($user, $enteredPassword);
        
            $user->setPassword($encoded); 
           
            
   
            $em = $this->getDoctrine()->getManager() ; 
            $em->persist($user) ; 
            $em->flush() ; 
         }
        }  
       
        else {
           return $this->json(['currentPasswordError'=> 'Check your Current Password']) ; 
        }

           
          
        return $this->json('Updated successfully') ; 
    }




    /**
     * @Route("/editImage/{id}", name="user_editImage", methods={"POST"})
     */
    public function editImage(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        
        $user = new User() ; 
        
        $user = $this->getDoctrine()->getRepository(User::class)->find($id) ;  
        $parameter = json_decode($request->getContent(),true) ; 
        
        
       
        $file = $request->files->get('file') ;   
        
        
        
        $name = rand(9999, 9999999999);
        $extension = $file->guessExtension();

        $fichier = $request->getSchemeAndHttpHost() . "/Profileimages" . '/' . $name . "." . $extension;    
       
        $file->move(
                $this->getParameter('uploads_directory'), 
                $fichier
            );
      
       $user->setImage($fichier);  

    
       $em = $this->getDoctrine()->getManager() ; 
 
       $em->persist($user) ;  
      
       $em->flush() ; 
   
    return  $this->json("Image updated successfuly");
 
        
        
    }  


    /**
     * @Route("/delete/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager,$id): Response
    {
           $user = $this->getDoctrine()->getRepository(User::class)->find($id) ;  
            $entityManager->remove($user); 
            $entityManager->flush();
        
        return $this->json("Deleted succefully");
    }  

    /**
     * @Route("/verify/email", name="app_verify_email", methods={"GET"})
     */
    public function verifyEmail(Request $request, User $user, EntityManagerInterface $entityManager,$id): Response
    {
          
        $user =  $tokenStorage->getToken()->getUser() ; 
       
        $user->setIsVerified(1) ; 
       
       return ($this->json(['sucess'=>'Email confirmed'])) ; 
    } 

      /**
     * @Route("/send/sms", name="sendSMS", methods={"POST"})
     */
    public function sendSMS(Request $request,  EntityManagerInterface $entityManager): Response
    {

            
            $sid    = "AC27324bec140e6ba899b305613169b357"; 
            $token  = "5c371ab33aec30e8622ef0cc206a611a"; 
            $twilio = new Client($sid, $token); 
            $numeros = ['+21650792380','21652021396'] ; 
            foreach($numeros as $num) 
                {     
                    $message = $twilio->messages 
                    ->create($num, // to 
                            array(  
                               
                                "messagingServiceSid" => "MGf73dd7f6d03753562915d8fb17346792",      
                                "body" => "ddd " 
                            ) 
                    ); }
       
            
                            return $this->json(['sms'=>'Send Succefully']) ; 
                
        
        }

    /**
     * @Route("/request/verif", name="verifMailRequest", methods={"POST"})
     */
    public function requestVerif(Request $request, 
     EntityManagerInterface $entityManager,\Swift_Mailer $mailer,TokenStorageInterface $tokenStorage ): Response
    {

            
        
         $user = $tokenStorage->getToken()->getUser() ;   
         $user->setIsVerified(2) ; 
         $message = (new \Swift_Message(' Pol , Confirm Email'))
                
         ->setFrom('Pol@gmail.com') 
         ->setTo($user->getEmail()) 
        
         ->setBody(
             $this->renderView(
                 'requestVerifcation/verif.html.twig', compact('user')
             ),
             'text/html'
         )
     ;
         $mailer->send($message);   

         return $this->json('sent') ; 
        
    }

   /**
     * @Route("/rand", name="rand", methods={"GET"})
     */
    public function rand(Request $request, 
     EntityManagerInterface $entityManager,\Swift_Mailer $mailer,TokenStorageInterface $tokenStorage ): Response
    {
        $user = $tokenStorage->getToken()->getUser() ; 

      

       //Start point of our date range.
            $start = strtotime("10 September 2000");

            //End point of our date range.
            $end = strtotime("22 July 2010");

            //Custom range.
            $timestamp = mt_rand($start, $end);

            //Print it out.
            $datewi =  date("Y-m-d", $timestamp);

            $user->setDateAjout( \DateTime::createFromFormat('yy-mm-dd hh:mm:ss', $timestamp)) ; 

        return  $this->json($string) ; 
    } 






    }
