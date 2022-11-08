<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use App\Entity\Domaine;
use App\Repository\TypeRepository;
use App\Repository\DomaineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/pol/type")
 */
class TypeController extends AbstractController
{


    /**
     * @Route("/", name="type_index", methods={"GET"})
     */
    public function index(TypeRepository $typeRepository): Response
    {
        $Types =   $typeRepository->findAll() ; 
        

        $TypeArray = [] ; 
      

        foreach($Types as $type)
         {
           
              $TypeArray[]=$type->toArray() ; 
               
         }

    
         
         return $this->json($TypeArray) ; 
    }










    /**
     * @Route("/new", name="type_new", methods={"POST"})
     */
    public function new(Request $request , DomaineRepository $DomaineRepository,TokenStorageInterface $tokenStorage): Response
    {
        $parameter = json_decode($request->getContent(),true) ; 
        
        $type = new Type() ; 

        $type->setTitle($request->get('title')) ; 
      
         $domaine = $DomaineRepository->findOneBy ([
             "title" => $request->get('domaine'), 
            
         ]) ; 

        
        $type->setDomaine($domaine);  
        
        $file = $request->files->get('file') ;   
        
        $name = rand(9999, 9999999999);
       
        $extension = $file->guessExtension();
  
        $fichier = $request->getSchemeAndHttpHost() . "/Profileimages" . '/' . $name . "." . $extension;    
       
        $file->move(
                $this->getParameter('uploads_directory'), 
                $fichier
            );
      
        $type->setImage($fichier);               
  

        $em = $this->getDoctrine()->getManager() ; 

        $em->persist($type) ;
        
        $em->flush() ; 

        return $this->json('Inserted successfully') ; 
    }



   /**
     * @Route("/{id}", name="competence_show", methods={"GET"})
     */
    public function showCompetence(Type $type,$id)
    {
        $type = $this->getDoctrine()->getRepository(Type::class)->find($id) ; 


        $competence = $type->getCompetences();  


        return $this->json($competence);   
    }



    /**
     * @Route("/edit/{id}", name="type_edit", methods={"POST"})
     */
    public function edit(Request $request, $id,DomaineRepository $DomaineRepository,TokenStorageInterface $tokenStorage,\Swift_Mailer $mailer): Response
    {
       
        $type = $this->getDoctrine()->getRepository(Type::class)->find($id) ; 
        
        //$parameter = json_decode($request->getContent(),true) ; 
        
        
       
    
        $domaine = $DomaineRepository->findOneBy ([
            "title" => $request->get('domaine'),
            
        ]) ;
        
        $type->setTitle($request->get('title')) ;   
        $type->setDomaine($domaine);  
        
        
        $file = $request->files->get('file') ;   
        
        if(!empty($file))
       {
            $name = rand(9999, 9999999999);
       
        $extension = $file->guessExtension();
  
        $fichier = $request->getSchemeAndHttpHost() . "/Profileimages" . '/' . $name . "." . $extension;    
       
        $file->move(
                $this->getParameter('uploads_directory'), 
                $fichier
            );
      
        $type->setImage($fichier);   
        }
        
      
        $em = $this->getDoctrine()->getManager() ; 
       
        $em->persist($type) ; 
       
        $em->flush() ; 

        return $this->json('Updated successfully') ; 


    }

    /**
     * @Route("/delete/{id}", name="type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id): Response
    {
        
        $data = $this->getDoctrine()->getRepository(Type::class)->find($id) ; 
        $em = $this->getDoctrine()->getManager() ; 
        $em->remove($data) ;
        $em->flush() ; 

        return $this->json('Deleted successfully') ;  

    }
}
