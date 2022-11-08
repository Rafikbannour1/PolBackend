<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Form\CompetenceType;
use App\Repository\TypeRepository;
use App\Repository\CompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/pol/competence")
 */
class CompetenceController extends AbstractController
{
    /**
     * @Route("/", name="competence_index", methods={"GET"})
     */
    public function index(CompetenceRepository $competenceRepository): Response
    {
        $Competences =   $competenceRepository->findAll() ;  
    
        $CompetenceArray = [] ; 

        foreach($Competences as $competence)
         {
              $CompetenceArray[]=$competence->toArray() ; 
         }
         return $this->json( $CompetenceArray) ;  
    } 


  



    /**
     * @Route("/new", name="competence_new", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager 
                       ,TypeRepository $typeRepository , TokenStorageInterface $tokenStorage ,\Swift_Mailer $mailer): Response
    {
        $parameter = json_decode($request->getContent(),true) ; 
        
        $competence = new Competence() ; 
       
        $competence->setTitle($parameter["title"]) ; 
      
         $type = $typeRepository->findOneBy ([
            "title" => $parameter['type'],
            
         ]) ; 

        $competence->setType($type);         
        
   
        $contact = $tokenStorage->getToken()->getUser()->getEmail() ; 
        
        if($contact!="rafik.bannour99@gmail.com")
          {  
        
        $message = (new \Swift_Message('New notification'))
                
        ->setFrom('noOne@gmail.com') 
        ->setTo('rafik.bannour99@gmail.com')
       
        ->setBody(
            $this->renderView(
                'emails/competenceNotify.html.twig', compact('contact')
            ),
            'text/html'
        )
    ;
        $mailer->send($message); 
        }
        $em = $this->getDoctrine()->getManager() ; 

        $em->persist($competence) ;
        
        $em->flush() ; 

        





        return $this->json('Inserted successfully') ; 
    }

    /**
     * @Route("/{id}", name="Document_show", methods={"GET"}) 
     */
    public function showDocuments($id): Response
    {
        $competence = $this->getDoctrine()->getRepository(Competence::class)->find($id) ; 


        $documents = $competence->getDocuments();  


        return $this->json($documents);    
    }

    /**
     * @Route("/edit/{id}", name="competence_edit", methods={"PUT"})
     */
    public function edit(Request $request, Competence $competence, EntityManagerInterface $entityManager,TypeRepository $typeRepository,$id): Response
    {
        $data = $this->getDoctrine()->getRepository(Competence::class)->find($id) ; 
        
        $parameter = json_decode($request->getContent(),true) ; 
        
        
        $data->setTitle($parameter['title']) ;  
    
        $type = $typeRepository->findOneBy ([
            "title" => $parameter['type'], 
            
        ]) ; 
       
        $data->setType($type);  

        $em = $this->getDoctrine()->getManager() ; 
       
        $em->persist($data) ; 
       
        $em->flush() ; 

        return $this->json('Updated successfully') ; 


    }

    /**
     * @Route("/delete/{id}", name="competence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Competence $competence, EntityManagerInterface $entityManager,$id): Response
    {
        $data = $this->getDoctrine()->getRepository(Competence::class)->find($id) ; 
        $em = $this->getDoctrine()->getManager() ; 
        $em->remove($data) ;
        $em->flush() ; 

        return $this->json('Deleted successfully') ;  
    }

    
}
