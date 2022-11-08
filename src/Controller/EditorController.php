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
    
/**
* @Route("/pol/editor", name="editor")
*/
class EditorController extends AbstractController
{
     /**
     * @Route("/", name="editor_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
       
        $editors =   $userRepository->findAll() ;  
        
        $editorArray = [] ; 

        foreach($editors as $editor)
         {
 
             $roles=$editor->getRoles();   
                
         if(in_array("ROLE_EDITOR",$roles)) 
              
            $editorArray[]=$editor->toArray() ; 
               
         }
 
         return $this->json($editorArray) ; 


    }


    /**
     * @Route("/newEditor", name="editor_new", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager,UserPasswordEncoderInterface $encoder): Response
    {
       
        $parameter = json_decode($request->getContent(),true) ; 
        
        $user = new User() ; 

        $user->setEmail($parameter['email']) ;  
        
        $encoded = $encoder->encodePassword($user, $parameter['password']);
        
        $user->setPassword($encoded);    
        
        $user->setName($parameter['name']) ; 
        $user->setBio($parameter['bio']) ; 
        $user->setMetier($parameter['metier']) ; 
        $user->setAdresse("No adress !") ; 
        $user->setTelephoneNumber(0) ;
        $user->setCodeSecurite('') ;  
       
        $user->setDateAjout(new \DateTime()) ;  
        $user->setRoles(['ROLE_EDITOR']) ;  
    
       
       
       $user->setImage($parameter['image']); 
        
       $em = $this->getDoctrine()->getManager() ; 

       $em->persist($user) ;  
       
       $em->flush() ; 
    
    return  $this->json($user);    

    }

    /**
     * @Route("/update/{id}", name="editor_edit", methods={"PUT"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,$id,UserPasswordEncoderInterface $encoder): Response
    {
        $data = $this->getDoctrine()->getRepository(User::class)->find($id) ; 
        $parameter = json_decode($request->getContent(),true) ; 
        
        

         $data->setEmail($parameter['email']) ; 
         $encoded = $encoder->encodePassword($data, $parameter['password']);
         $data->setPassword($encoded); 
         $data->setBio($parameter['bio']) ;  
         $data->setName($parameter['name']) ;  
         $data->setMetier($parameter['metier']) ;  
         $data->setAdresse($parameter['adresse']) ;  
         $data->setRoles(['ROLE_EDITOR']) ; 
         $data->setImage($parameter['image']) ;  
         $data->setDateAjout(new \DateTime()) ; 

        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($data) ; 
       
        $em->flush() ; 

        return $this->json('Updated successfully') ; 
    }


    /**
     * @Route("/delete/{id}", name="editor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager,$id): Response
    {
           $user = $this->getDoctrine()->getRepository(User::class)->find($id) ;  
            $entityManager->remove($user); 
            $entityManager->flush();
        
        return $this->json("Deleted succefully");
    }  



    





    
}
