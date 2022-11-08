<?php

namespace App\Controller;

use App\Entity\Likes;
use App\Form\LikesType;
use App\Entity\Document;
use App\Repository\LikesRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/pol/likes")
 */
class LikesController extends AbstractController
{
    /**
     * @Route("/", name="likes_index", methods={"GET"})
     */
    public function index(LikesRepository $likesRepository): Response
    {
        $likes =   $likesRepository->findAll() ;  

        $likesArray = [] ; 

            foreach($likes as $like)
             {
                   $likesArray[] = $like->toArray()  ;   
             }
          
             return $this->json($likesArray) ;  
    }


    /**
     * @Route("/new/{id}", name="likes_new", methods={"POST"})
     */
    public function like(Request $request, EntityManagerInterface $entityManager,
    DocumentRepository $documetRepositor ,TokenStorageInterface $tokenStorage ,$id): Response
    {
        $like = new Likes(); 
        
       
        $document = $this->getDoctrine()->getRepository(Document::class)->find($id) ; 
        $user =  $tokenStorage->getToken()->getUser() ; 
    
        $like->setDocuments($document) ;  
       
        $like->setUser($user) ; 

        $document->addLike($like) ; 

        $em = $this->getDoctrine()->getManager() ; 
       
        $em->persist($like) ; 
       
        $em->flush() ; 
       
        return $this->json($like) ; 
      

    }



    /**
     * @Route("/unlike/{id}", name="unlike", methods={"DELETE"})
     */
    public function unlike(Request $request, EntityManagerInterface $entityManager,
    DocumentRepository $documetRepositor ,TokenStorageInterface $tokenStorage ,$id,LikesRepository $likesRepository): Response
    {
        $likes = $likesRepository->findAll() ;  
        $document = $this->getDoctrine()->getRepository(Document::class)->find($id) ; 
        $em = $this->getDoctrine()->getManager() ; 
       
        $boolean = false ; 
        foreach($likes as $like)
        {  
            if($like->getDocuments() == $document)
             {

                $boolean = true ; 
              
             }
        }
        if($boolean == true)
            {
                $em->remove($like) ; 
                $em->flush() ; 
            }
   
             
        return $this->json('Deleted successfully') ; 
      

    }








    
}
