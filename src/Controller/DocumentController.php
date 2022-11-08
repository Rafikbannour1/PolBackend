<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Images;
use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\UserRepository;
use App\Repository\GroupeRepository;
use App\Repository\ImagesRepository;
use App\Repository\DocumentRepository;
use App\Repository\CompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



/**
 * @Route("/pol/document")
 */
class DocumentController extends AbstractController
{
    /**
     * @Route("/search", name="document_filter", methods={"GET"})
     */
    public function search(DocumentRepository $documentRepository,Request $request): Response
    {

         $documents =   $documentRepository->findAll() ;  
         $documentTitle = $request->get('title') ; 
         $competenceSearch = $request->get('competence') ;  
         $domaineSearch = $request->get('domaine') ;   
         $typeSearch = $request->get('type') ;  
         $documentArray = [] ; 
 
             foreach($documents as $document)
              {
              
            
            if( !empty($documentTitle)  || !empty($competenceSearch ) || !empty( $domaineSearch) || !empty( $typeSearch))   
               {    
               
                if(($document->getTitle() == $documentTitle) ||
                $document->getCompetence()->getTitle() == $competenceSearch ||
                $document->getCompetence()->getType()->getTitle() == $typeSearch ||
              ($document->getCompetence()->getType()->getDomaine()->getTitle() == $domaineSearch )
                )
                {
                    $documentArray[] = $document->toArray()  ;  
                }
              }
           
           
              else {
                $documentArray[] = $document->toArray()  ; 
            }  
              
        
        
        }
     
             
        
        
        
        return $this->json($documentArray) ;  

        
    }



     /**
     * @Route("/true", name="document_get_true", methods={"GET"})
     */
    public function index(DocumentRepository $documentRepository,Request $request): Response
    {

         $documents =   $documentRepository->findAll() ;  
         
        
         
            $documentArray = [] ; 
            

             foreach($documents as $document)
              {    
                   if($document->getEtat() == "true") 

                        $documentArray[] = $document->toArray()  ; 
                     
              }
           
              return $this->json($documentArray) ; 

        
    }

     /**
     * @Route("/", name="document_get_all", methods={"GET"})
     */
    public function indexFalse(DocumentRepository $documentRepository,Request $request): Response
    {

         $documents =   $documentRepository->findAll() ;  
         
        
         
            $documentArray = [] ; 
            

             foreach($documents as $document)
              {    
               
                        $documentArray[] = $document->toArray()  ; 
                     
              }
           
              return $this->json($documentArray) ; 

        
    }


      /**
     * @Route("/getLikes", name="getLikes", methods={"GET"})
     */
    public function getLikes(DocumentRepository $documentRepository,Request $request): Response
    {

         $documents =   $documentRepository->findAll() ;  

            
           
              return $this->json($documents[0]->getLikes()) ; 

        
    }

     
    /**
     * @Route("/getTypes", name="document_types", methods={"GET"})
     */

    function getTypes(DocumentRepository $documentRepository )
    {
        
        $documents =   $documentRepository->findAll() ; 
           
                $typesArray = [] ; 
            foreach($documents as $document)
             {
                  $typesArray[]=$document->getCompetence()->getType() ;  
                   
             }
     
             return $this->json($typesArray) ; 

    }
    
    /**
     * @Route("/new", name="document_new", methods={"POST"}) 
     */
    public function new(Request $request, 
    ImagesRepository $imageRepository  , 
    EntityManagerInterface $entityManager ,
     UserRepository $userRepository ,
     CompetenceRepository $competenceRepository ,\Swift_Mailer $mailer ,TokenStorageInterface $tokenStorage): Response
    {

        $document = new Document() ; 

        $document->setTitle($request->get('title')) ;  
        
        $document->setDescription($request->get('description')) ;   
        
        $user = $this->getDoctrine()->getRepository(User::class);
    
        $document->setAuteur($tokenStorage->getToken()->getUser()) ;    
        
        $document->setEtat('false') ; 
        $document->setDateAjout(new \DateTime('now')) ;  
        
        $competence = $competenceRepository->findOneBy ([
            "id" => $request->get('competence'), 
            
         ]) ; 

        $document->setCompetence($competence) ; 

        $files = $request->files->get('files');    
        
            

        foreach($files as $file) {     

            
            $name = rand(9999, 9999999999);
            $extension = $file->guessExtension();

            $fichier = $request->getSchemeAndHttpHost() . "/uploads" . '/' . $name . "." . $extension;   
        
            $file->move(
                $this->getParameter('upload_dir'), 
                $fichier
            );
       
        $img = new Images();
        
        $img->setName($fichier);
        $img->setDescription('') ; 
        $document->addImage($img);  
    }
            

            $contact = "editor@gmail.com" ; 
            $message = (new \Swift_Message('Hi!'))       
            ->setFrom('noOne@gmail.com') 
            ->setTo('rafik.bannour99@gmail.com') 
            ->setBody(
                $this->renderView(
                    'emails/DocumentsNotify.html.twig', compact('contact')
                ),
                'text/html'
            )
        ;
       
        $mailer->send($message); 
      
       $em = $this->getDoctrine()->getManager() ; 

       $em->persist($document) ;  
       
       $em->flush() ;
    
    
    return  $this->json($document);    
    }
   
   
    // /**
    //  * @Route("/addFiles/{id}", name="document_addFiles", methods={"POST"}) 
    //  */
    // public function addFiles(Request $request, EntityManagerInterface $entityManager,
    // CompetenceRepository $competenceRepository,$id,TokenStorageInterface $tokenStorage): Response
    // {
    //     $document = $this->getDoctrine()->getRepository(Document::class)->find($id) ; 
         

    //     $files = $request->files->get('files');    
       
    //     foreach($files as $file) {     

            
    //         $name = rand(9999, 9999999999);
    //         $extension = $file->guessExtension();

    //         $fichier = $request->getSchemeAndHttpHost() . "/uploads" . '/' . $name . "." . $extension;   
        
    //         $file->move(
    //             $this->getParameter('upload_dir'), 
    //             $fichier
    //         );
        
    //         $img = new Images();
            
    //         $img->setName($fichier);
    //         $img->setDescription('') ; 
    //         $document->addImage($img);  
    // }

    //     $em = $this->getDoctrine()->getManager() ; 
       
    //     $em->persist($document) ; 
       
    //     $em->flush() ; 

    //     return $this->json('Updated successfully') ; 
    // }

    /**
     * @Route("/edit/{id}", name="document_edit", methods={"POST"}) 
     */
    public function edit(Request $request, EntityManagerInterface $entityManager,
    CompetenceRepository $competenceRepository,$id,TokenStorageInterface $tokenStorage,ImagesRepository $fileRepository): Response
    {
        $document = $this->getDoctrine()->getRepository(Document::class)->find($id) ; 
        
        $parameter = json_decode($request->getContent(),true) ; 
        

        $document->setTitle($request->get('title')) ;  
        
        $document->setDescription($request->get('description')) ;   
       
     
        $competence = $competenceRepository->findOneBy ([
            "title" => $request->get('competence'), 
            
         ]) ; 

       
        $document->setCompetence($competence) ; 
    
        $document->setAuteur($tokenStorage->getToken()->getUser()) ;    
        
        $document->setEtat('true') ; 
        $document->setDateAjout(new \DateTime('now')) ;  

        $competence = $competenceRepository->findOneBy ([
            "id" => $request->get('competence'), 
            
         ]) ; 

       
        
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
       
        $file = new Images();
        $file->setName($fichier);
        $file->setDescription($File->getClientOriginalName()) ; 
        $document->addImage($file);   
    }
        }
  
       
       
        $filesToremove = $request->get('filesToremove') ; 
        if(!empty($filesToremove))
          
          {
              foreach($filesToremove as $fileToremove)
             {
               
                 $document->removeImage($fileRepository->find($fileToremove)) ; 
             }
          }

        $em = $this->getDoctrine()->getManager() ; 
       
        $em->persist($document) ; 
       
        $em->flush() ; 

        return $this->json('Updated successfully') ; 
    }



    /**
     * @Route("/delete/{id}", name="document_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager,$id): Response
    {
        $data = $this->getDoctrine()->getRepository(Document::class)->find($id) ; 
        $em = $this->getDoctrine()->getManager() ; 
        $em->remove($data) ; 
        $em->flush() ; 
             
        return $this->json('Deleted successfully') ; 
    }

    
     /**
      * @Route("/deleteFiles/{id}", name="document_deleteFiles", methods={"DELETE"})
      */
     public function deleteFiles(Request $request, EntityManagerInterface $entityManager,$id): Response
     {
         $image = $this->getDoctrine()->getRepository(Images::class)->find($id) ; 

         $em = $this->getDoctrine()->getManager() ; 
         $em->remove($image) ; 
         $em->flush() ; 
             
         return $this->json('Deleted successfully') ; 
     }


    /**
     * @Route("/editEtat/{id}", name="document_editEtat", methods={"PUT"})
     */
    public function editEtat(Request $request, Document $document, EntityManagerInterface $entityManager,$id): Response
    {
        $data = $this->getDoctrine()->getRepository(Document::class)->find($id) ; 
        
        $parameter = json_decode($request->getContent(),true) ; 
        
        
        $data->setEtat("true") ;  
        
        
        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($data) ; 
        $em->flush() ; 
             
        return $this->json('Updated successfully') ; 
    }

    /**
     * @Route("/like/{id}", name="document_like", methods={"POST"})
     */
    public function like(Request $request, Document $document, EntityManagerInterface $entityManager,$id): Response
    {
        $document = $this->getDoctrine()->getRepository(Document::class)->find($id) ; 
        
        $parameter = json_decode($request->getContent(),true) ; 
        
        $like = $document->getLikes() ; 

        dd($like) ; 

        $document->setLikes($like+1) ;  
        
        
        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($document) ; 
        $em->flush() ; 
             
        return $this->json('you liked this post ') ; 
    }


    /**
     * @Route("/{id}", name="getDocument", methods={"GET"})
     */
    public function getOneDocument(DocumentRepository $documentRepository,Request $request,$id): Response
    {

        $document = $this->getDoctrine()->getRepository(Document::class)->find($id) ;  
       
        $documentArray = $document->toArray() ; 
        
        return $this->json($documentArray) ;   
        
    }  
    
    

     
    /**
     * @Route("/get/competenceStat", name="postsLikes", methods={"GET"})
     */
    public function postsLikes(CompetenceRepository $competenceRepository,DocumentRepository $documentRepository  ): Response
    {
        $competences =   $competenceRepository->findAll() ;  
        $documents = $documentRepository->findAll() ;  

        $likesArray = [] ; 
        $competenceArray = [] ; 
     
        $NbPostsArray = [] ; 

           foreach($competences as $competence)
              {         
                  if(count($competence->getDocuments()) > 0)
                   {

                       $competenceArray [] = $competence->getTitle() ; 
                       $NbPostsArray [] = count($competence->getDocuments()) ; 
                   
                

                   $likes = 0 ; 
                foreach($competence->getDocuments() as $document)
                    {
                        
                             $likes += count($document->getLikes()) ; 
                             $PostTitles = $document->getTitle() ;    
                      
                          
                    }

                      $likesArray [] = $likes ;  
                       
                    }
                    
             }
            
            
          
            return $this->json([
                'competences'=>$competenceArray,
                'nbPosts' => $NbPostsArray,
                'nbLikes'=>$likesArray ]) ; 

    }

}
