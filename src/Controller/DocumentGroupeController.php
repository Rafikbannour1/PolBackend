<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Entity\Videos;
use App\Entity\DocumentGroupe;
use App\Entity\FormationFiles;
use App\Form\DocumentGroupeType;
use App\Repository\GroupeRepository;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentGroupeRepository;
use App\Repository\FormationFilesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/pol/documentGroupe")
 */
class DocumentGroupeController extends AbstractController
{
    /**
     * @Route("/", name="document_groupe_index", methods={"GET"})
     */
    public function index(DocumentGroupeRepository $documentGroupeRepository): Response
    {
        $documentsGroupe =   $documentGroupeRepository->findAll() ;  

        $documentGroupeArray = [] ; 

            foreach($documentsGroupe as $documentGroupe)
             {
                   $documentGroupeArray [] = $documentGroupe->toArray()  ;  
             }
          
             return $this->json($documentGroupeArray) ; 
    }

    /**
     * @Route("/new", name="document_groupe_new", methods={"POST"})
     */
    public function new(Request $request,GroupeRepository $groupeRepository,
    DocumentGroupeRepository $documentGroupeRepository,VideosRepository $videoRepository): Response
    {
       
        
        $documentGroupe = new DocumentGroupe() ; 
        
        $documents =  $documentGroupeRepository->findAll() ;  

        $groupe = $groupeRepository->findOneBy ([
            "id" => $request->get('groupe'), 
           
        ]) ; 
        $boolean = false ; 
        if(empty($documents))
            {
                $documentGroupe->setNumeroModule(strval(1)) ; 
            }
     
           
         else {
            foreach($documents as $document)
             {

                 if( $groupe->getId() == $document->getGroupe()->getId() )
                     {
                         $boolean = true ; 
                        
                        $numModule =  $document->getNumeroModule()+1 ; 
                         
                     }
                
             }
        }
             if($boolean== true)
                {
                    $documentGroupe->setNumeroModule(strval($numModule)) ; 
                }
             else {
                $documentGroupe->setNumeroModule(strval(1)) ; 
             }   

                
        
        $documentGroupe->setTitle($request->get('title')) ;    
        $documentGroupe->setDescription($request->get('description')) ; 
        $documentGroupe->setObjectif($request->get('objectif')) ; 
        $documentGroupe->setDateAjout(new \DateTime('now')) ;  
        
     

        $documentGroupe->setGroupe($groupe);        
        $files = $request->files->get('files');    
       
       
            if(!empty($files))
            {
        foreach($files as $file) {     

            
            $name = rand(9999, 9999999999);
            $extension = $file->guessExtension();

            $fichier = $request->getSchemeAndHttpHost() . "/uploads" . '/' . $name . "." . $extension;   
        
            $file->move(
                $this->getParameter('upload_dir'), 
                $fichier
            );
       
        $Formationfile = new FormationFiles();
        
        $Formationfile->setName($fichier);
        $Formationfile->setDescription($file->getClientOriginalName());
        $documentGroupe->addFormationFile($Formationfile);   
    }
            
}     
    

    $videos = $request->files->get('videos');
    $videoDescriptions = $request->get('VideoDescriptions');

             
    if(!empty($videos))
            {
        foreach($videos as $Video) {     
             
             
            $name = rand(9999, 9999999999);
            $extension = $Video->guessExtension();
            
            $fichier = $request->getSchemeAndHttpHost() . "/uploads" . '/' . $name . "." . $extension;   
        
            $Video->move(
                $this->getParameter('upload_dir'), 
                $fichier
            );
       
            $video = new Videos();
            
            $video->setName($fichier);
            
            $video->setDescription($Video->getClientOriginalName());

            $documentGroupe->addVideo($video); 
          
            
              

                      
    }
    
   
  
   
       
}  
       

        $em = $this->getDoctrine()->getManager() ; 

        $em->persist($documentGroupe) ;
        
        $em->flush() ; 

    
    
       return $this->json('Inserted successfully') ; 
    }

 

    /**
     * @Route("/editInfo/{id}", name="document_groupe_editInfo", methods={"POST"}) 
     */
    public function edit(Request $request,$id,VideosRepository $videoRepository,FormationFilesRepository $fileRepository): Response
    {
        $DocumentGroupe = $this->getDoctrine()->getRepository(DocumentGroupe::class)->find($id) ; 
        
       
       
         $parameter = json_decode($request->getContent(),true) ; 
         $DocumentGroupe->setTitle($request->get('title')) ;  
         $DocumentGroupe->setObjectif($request->get('objectif')) ; 
         $DocumentGroupe->setDescription($request->get('description')) ;    
         $DocumentGroupe->setDateAjout(new \DateTime('now')) ;  

    // update FormationVideos
        $videos = $request->files->get('videos');   
       
       
        if(!empty($videos))
        {   
      foreach($videos as $Video) {     

            
            $name = rand(9999, 9999999999);
            $extension = $Video->guessExtension();

            $fichier = $request->getSchemeAndHttpHost() . "/uploads" . '/' . $name . "." . $extension;   
        
            $Video->move(
                $this->getParameter('upload_dir'), 
                $fichier
            );
       
        $video = new Videos();
        $video->setName($fichier);
        $video->setDescription($Video->getClientOriginalName()) ; 
        $DocumentGroupe->addVideo($video);   
    }
        }



        ///update formationFiles
        $files = $request->files->get('files');   
       
        if(!empty($files))
        {   
      foreach($files as $File) {     

            
            $name = rand(9999, 9999999999);
            $extension = $File->guessExtension();

            $fichier = $request->getSchemeAndHttpHost() . "/uploads" . '/' . $name . "." . $extension;   
        
            $File->move(
                $this->getParameter('upload_dir'), 
                $fichier
            );
       
        $file = new FormationFiles();
        $file->setName($fichier);
        $file->setDescription($File->getClientOriginalName()) ; 
        $DocumentGroupe->addFormationFile($file);   
    }
        }
  
///remove files
          $videosToremove = $request->get('videosToremove') ; 
          $filesToremove = $request->get('filesToremove') ; 
          if(!empty($videosToremove))
          
          {
              foreach($videosToremove as $videoToremove)
             {
                 $DocumentGroupe->removeVideo($videoRepository->find($videoToremove)) ; 
                 
             }
          }

          if(!empty($filesToremove))
          
          {
              foreach($filesToremove as $fileToremove)
             {
               
                 $DocumentGroupe->removeFormationFile($fileRepository->find($fileToremove)) ; 
             }
          }
       
        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($DocumentGroupe) ; 
        $em->flush() ; 

        return $this->json('Updated successfully') ; 
    }
    

   /**
     * @Route("/{id}/{module}", name="document_groupe_show", methods={"GET"})
     */
    public function show($id,DocumentGroupeRepository $documentGrpRepository,Request $request,$module): Response
    {
        $groupe = $this->getDoctrine()->getRepository(Groupe::class)->find($id) ; 

        $parameter = json_decode($request->getContent(),true) ; 

      
        
        $documents = $documentGrpRepository->findAll() ; 

        $documentArray = [] ; 
       
        foreach($documents as $document)
            {
                if($document->getNumeroModule() == $module && $document->getGroupe()->getId()== $groupe->getId())  
                    {
                        $documentArray[]=$document; 
                    }
            }
        
        return $this->json($documentArray) ;  
    }



    /**
     * @Route("/delete/{id}", name="document_groupe_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager,$id): Response
    {
        $data = $this->getDoctrine()->getRepository(DocumentGroupe::class)->find($id) ; 
        $em = $this->getDoctrine()->getManager() ; 
        $em->remove($data) ; 
        $em->flush() ; 
             
        return $this->json('Deleted successfullyyyyf') ; 
    }

     /**
     * @Route("/{id}", name="getOneDocument", methods={"GET"})
     */
    public function getOneDocument(DocumentGroupeRepository $documentRepository,Request $request,$id): Response
    {

        $document = $this->getDoctrine()->getRepository(DocumentGroupe::class)->find($id) ;  
       
        $documentArray = $document->toArray() ; 
        
        return $this->json($documentArray) ;   

        
    }
}
