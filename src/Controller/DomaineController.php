<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Domaine;
use App\Form\DomaineType;
use App\Repository\DomaineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/pol/domaine")
 */
class DomaineController extends AbstractController
{
    /**
     * @Route("/", name="domaine_index", methods={"GET"})
     */
    public function index(DomaineRepository $domaineRepository): Response
    {
        $domaines =   $domaineRepository->findAll() ; 
        
        $domaineArray = [] ; 

        foreach($domaines as $domaine)
         {
              $domaineArray[]=$domaine->toArray() ;      
         }
 
         return $this->json($domaineArray) ; 
    }

    /**
     * @Route("/new", name="domaine_new", methods={"POST"})
     * 
     */
    public function new(Request $request, EntityManagerInterface $entityManager,\Swift_Mailer $mailer,
    DomaineRepository $domaineRepository, TokenStorageInterface $tokenStorage): Response
    {

        $parameter = json_decode($request->getContent(),true) ; 
        
        $domaine = new Domaine();
        
        $domaines = $domaineRepository->findAll() ;  

        $domaineRequest =  $request->get('title') ; 

        $domaineTitles = [] ; 

         foreach ($domaines as $Domaine )
             {
                 $domaineTitles [] = $Domaine->getTitle() ;
             }


               if(in_array( $request->get('title')  ,$domaineTitles))
                     {
                         return $this->json([
                             'domaineError'=> 'This Domaine is Already exists' 
                         ]) ;
                     }
            
         else 

      $domaine->setTitle($domaineRequest) ;  

      $file = $request->files->get('file') ;   
        
      $name = rand(9999, 9999999999);
     
      $extension = $file->guessExtension();

      $fichier = $request->getSchemeAndHttpHost() . "/Profileimages" . '/' . $name . "." . $extension;    
     
      $file->move(
              $this->getParameter('uploads_directory'), 
              $fichier
          );
    
       $domaine->setImage($fichier);               


        $contact = $tokenStorage->getToken()->getUser()->getEmail() ; 
        
    //     if($contact!="rafik.bannour99@gmail.com")
    //       {             
    //     $message = (new \Swift_Message('New notification')) 
       
    //     ->setFrom('noOne@gmail.com')        
        
    //     ->setTo('rafik.bannour99@gmail.com')
       
    //     ->setBody(
    //         $this->renderView(
    //             'emails/DomaineNotify.html.twig',compact("contact")
    //         ),
    //         'text/html'
    //     )
    // ;
    //     $mailer->send($message); 

    // }   


        $entityManager->persist($domaine);
        $entityManager->flush();

        return $this->json('Inserted successfully') ; 
     

       
    }

    /**
     * @Route("/{id}", name="domaine_show", methods={"GET"})
     *
     */
    public function showType(Domaine $domaine,$id)
    {
        $domaine = $this->getDoctrine()->getRepository(Domaine::class)->find($id) ; 


        $type = $domaine->getTypes() ;  


        return $this->json($type);   
    }

    
   

    //   /**
    //  * @Route("/formation/{id}", name="domaine_show", methods={"GET"})
    //  *
    //  */
    // public function showTypes($id) 
    // {
    //     $domaine = $this->getDoctrine()->getRepository(Domaine::class)->find($id) ; 

    //     return $this->json($domaine->getGroupes());   
        
        
    // }




    /**
     * @Route("/edit/{id}", name="domaine_edit", methods={"POST"})
     */
    public function edit(Request $request,$id): Response
    {
        $domaine = $this->getDoctrine()->getRepository(Domaine::class)->find($id) ; 
        

        $domaine->setTitle($request->get('title')) ;  
    
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
      
          $domaine->setImage($fichier);               
  
        }

        $em = $this->getDoctrine()->getManager() ; 
       
        $em->persist($domaine) ; 
       
        $em->flush() ; 

        return $this->json('Updated successfully') ; 

    }

    /**
     * @Route("/delete/{id}", name="domaine_delete", methods={"DELETE"})
     *
     */
    public function delete(Request $request,$id ): Response
    {
        $data = $this->getDoctrine()->getRepository(Domaine::class)->find($id) ; 
        $em = $this->getDoctrine()->getManager() ; 
        $em->remove($data) ;
        $em->flush() ; 

        return $this->json('Deleted successfully') ;  

    }

    
    /**
     * @Route("/get/domaineTitles", name="domaineTitles", methods={"GET"})
     */
    public function DomaineTitles(DomaineRepository $domaineRepository ): Response
    {
        $domaines =   $domaineRepository->findAll() ; 
        

        $domaineArray = [] ; 

        foreach($domaines as $domaine)
         {
              $domaineArray[]=$domaine->getTitle() ;      
         }
 
         return $this->json($domaineArray) ; 

    }



    /**
     * @Route("/get/CompetenceNumber", name="CompetenceNumber", methods={"GET"})
     */
    public function CompetenceNumber(DomaineRepository $domaineRepository ): Response
    {
        $domaines =   $domaineRepository->findAll() ;  
        

        $domaineArray = [] ; 

        foreach($domaines as $domaine)
         {
              $domaineArray[]= count($domaine->getTypes())  ;       
         }
 
         return $this->json($domaineArray) ; 

    }

   
}
