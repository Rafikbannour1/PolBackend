<?php

namespace App\Controller;

use App\Entity\Type;
use App\Entity\Groupe;
use App\Entity\Domaine;
use App\Form\GroupeType;
use App\Entity\Competence;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\GroupeRepository;
use App\Repository\DomaineRepository;
use App\Repository\DocumentRepository;
use App\Repository\CompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/pol/groupe")
 */
class GroupeController extends AbstractController
{

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
   
    /**
     * @Route("/", name="groupe_index", methods={"GET"})
     */
    public function index(GroupeRepository $groupeRepository): Response
    {
        $groupes =   $groupeRepository->findAll() ;  

        $groupeArray = [] ; 

            foreach($groupes as $groupe)
             {
                   $groupeArray[] = $groupe->toArray()  ;  
             }
          
             return $this->json($groupeArray) ; 
    }

    /**
     * @Route("/new", name="groupe_new", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager,
    TokenStorageInterface $tokenStorage,CompetenceRepository $competenceRepository,
    UserRepository $userRepository,\Swift_Mailer $mailer): Response
    {
       $groupe = new Groupe () ; 
       
       $parameter = json_decode($request->getContent(),true) ; 
       
       $groupe->setTitle($request->get('title')) ; 

       $groupe->setPrix($request->get('price')) ; 
       $groupe->setLangue($request->get('langue')) ; 
       $groupe->setRateValue(0) ; 

       $groupe->setEtat('false') ; 

       $groupe->setEvaluation('false') ; 
      
       $groupe->setDateAjout(new \DateTime('now')) ;  
       
       $groupe->setObjectif($request->get('objectif')) ; 
      
       $user = $tokenStorage->getToken()->getUser() ; 

       $groupe->setUser($user) ; 
     
       $competence = $competenceRepository->findOneBy ([
        "title" => $request->get('competence'), 
        
        ]) ; 

      $groupe->setCompetence($competence) ; 
       
      $file = $request->files->get('file') ;   
        
      $name = rand(9999, 9999999999);
     
      $extension = $file->guessExtension();

      $fichier = $request->getSchemeAndHttpHost() . "/Profileimages" . '/' . $name . "." . $extension;    
     
      $file->move(
              $this->getParameter('uploads_directory'), 
              $fichier
          );
    
       $groupe->setImage($fichier);  
      

        
       $em = $this->getDoctrine()->getManager() ; 
       $em->persist($groupe) ;  
       $em->flush() ;
        
       $users = $userRepository->findAll() ; 

       foreach($users as $User)
       {     
      
      $message = (new \Swift_Message('New Training added to'.' '.$competence->getTitle().'  '.' from'.' '
               .$groupe->getUser()->getName()))
                            
               ->setFrom('Pol@gmail.tn', 'Pol')  
           
               ->setTo($User->getEmail())
          
               ->setBody(
                   $this->renderView(
                      'emailNotification/AddFormationNotif.html.twig'
                   ),
                   'text/html'
               )
               ;
               $mailer->send($message);
           }
    
        return  $this->json("good");   

    }

     /**
     * @Route("/edit/{id}", name="groupe_edit", methods={"PUT"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager,$id,
    TokenStorageInterface $tokenStorage): Response
    {
        $groupe = $this->getDoctrine()->getRepository(Groupe::class)->find($id) ;
       
       $parameter = json_decode($request->getContent(),true) ; 
       
       $groupe->setTitle($parameter['title']) ; 
       
       $groupe->setDescription($parameter['description']) ; 
       
       $groupe->setImage('https://www.bootdey.com/app/webroot/img/bg9.jpg') ; 

       $user = $tokenStorage->getToken()->getUser() ; 

       $groupe->setUser($user) ; 
        
       $em = $this->getDoctrine()->getManager() ; 
       $em->persist($groupe) ;  
       $em->flush() ;
    
    
        return  $this->json("Updated");   

    }
    

    /**
     * @Route("/groupefromType/{id}", name="getGroupes", methods={"GET"})
     */
    public function getGroupes(GroupeRepository $groupeRepository,Request $request,$id): Response
    {

        $type = $this->getDoctrine()->getRepository(Type::class)->find($id) ;   
        $groupes = $groupeRepository->findAll() ; 

        $groupeArray = [] ;
        
        foreach ($groupes as $groupe)
            { 
          
                if($groupe->getCompetence()->getType()->getId() == $type->getId()) 
                    {
                        $groupeArray[] = $groupe->toArray() ; 
                    }
            }

        
        return $this->json($groupeArray) ;    
 
    } 


    /**
     * @Route("/get/{id}", name="getOneFormationnnnnnnnn", methods={"GET"})
     */
    public function getOneGroupe(Request $request,$id): Response
    {
     
        $groupe = $this->getDoctrine()->getRepository(Groupe::class)->find($id) ;  
        
        
        return $this->json($groupe->toArray()) ;    
       

       
 
    } 

    


    /**
     * @Route("/mesFormations", name="MesFormations", methods={"GET"}) 
     */
    public function GetMesFormations(Request $request,TokenStorageInterface $tokenStorage): Response
    {
          $CurrentUser = $tokenStorage->getToken()->getUser() ;  

           $mesFormations =$CurrentUser->getCours() ; 
           
           $mesFormationsArray = [] ; 

           foreach ($mesFormations as $formation)
            {
                $mesFormationsArray [] = $formation->toArray() ; 
            }
            
           
           return $this->json($mesFormationsArray)  ; 


    }

    /**
     * @Route("/delete/{id}", name="groupe_delete", methods={"DELETE"}) 
     */
    public function delete(Request $request,$id): Response
    {
        $groupe = $this->getDoctrine()->getRepository(Groupe::class)->find($id) ; 
        
        
        
        $em = $this->getDoctrine()->getManager() ; 
        $em->remove($groupe)  ; 
        $em->flush() ; 

        return $this->json('Deleted successfully') ;  
    }

    
    /**
     * @Route("/getCurrentUserFormations", name="groupe_get_getCurrentUserFormations", methods={"GET"}) 
     */
    public function currentUserFormations(Request $request,TokenStorageInterface $tokenStorage): Response
    {
        $CurrentUser = $tokenStorage->getToken()->getUser() ;  

           $mesFormations =$CurrentUser->getGroupes() ;  
           
           $mesFormationsArray = [] ; 

           foreach ($mesFormations as $formation)
            {
                $mesFormationsArray [] = $formation->toArray() ; 
            }
            
           
           return $this->json($mesFormationsArray)  ; 


    }


    /**
     * @Route("/evaluer/{id}", name="Evaluer", methods={"POST"}) 
     */
    public function Evaluer(Request $request,$id): Response
    {
        
        $groupe = $this->getDoctrine()->getRepository(Groupe::class)->find($id) ; 

        $groupe->setEvaluation('true'); 

        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($groupe)  ; 
        $em->flush() ; 

        return $this->json('Evaluation Updated');  

    }
    

    /**
     * @Route("/editEtat/{id}", name="EditEtat", methods={"PUT"}) 
     */
    public function EditEtat(Request $request,$id): Response
    {
        
        $groupe = $this->getDoctrine()->getRepository(Groupe::class)->find($id) ; 

        $groupe->setEtat('true'); 

        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($groupe)  ; 
        $em->flush() ; 

        return $this->json('Etat Updated');  

    }

     /**
     * @Route("/get/coursesAchetee/a77", name="coursesAchetee", methods={"GET"}) 
     */
    public function coursesAchetee(Request $request,GroupeRepository $groupeRepository,UserRepository $userRepository): Response
    {
        $groupes = $groupeRepository->findAll() ; 
        $users = $userRepository->findAll() ;    
        $CoursArray = [] ; 
        
    

        foreach($groupes as $groupe)
            {
                $CoursArray [] =  $groupe->getTitle() ; 
                $nmuberOfSellingsArray [] = count($groupe->getUsers()) ;  
            }    

     

    
           return $this->json([
               'cours'=> $CoursArray,
               'numberOfSellings' => $nmuberOfSellingsArray 
                
               ]) ; 
    }


    
     /**
     * @Route("/get/Historique/Achat", name="HistoriqueAchat", methods={"GET"}) 
     */
    public function HistoriqueAchat(Request $request,GroupeRepository $groupeRepository,UserRepository $userRepository): Response
    {
        $groupes = $groupeRepository->findAll() ; 
        $users = $userRepository->findAll() ;    
        $CoursArray = [] ; 
        
    

        foreach($groupes as $groupe)
            {
                $CoursArray [] =  $groupe->getTitle() ; 
                $nmuberOfSellingsArray [] = count($groupe->getUsers()) ;  
            }    

     

    
           return $this->json([
               'cours'=> $CoursArray,
               'numberOfSellings' => $nmuberOfSellingsArray 
                
               ]) ; 
    }

   
  
    
    /**
     * @Route("/edit/info/{id}", name="HistoriqueAchat", methods={"post"}) 
     */
    public function EditInfo(Request $request,GroupeRepository $groupeRepository,UserRepository $userRepository,$id
    , TokenStorageInterface $tokenStorage,CompetenceRepository $competenceRepository): Response
    {

        $groupe = $this->getDoctrine()->getRepository(Groupe::class)->find($id) ; 

        $groupe->setTitle($request->get('title')) ; 

        $groupe->setPrix($request->get('price')) ; 
        $groupe->setLangue($request->get('langue')) ; 
        $groupe->setRateValue(0) ; 
 
        $groupe->setEtat('false') ; 
 
        $groupe->setEvaluation('false') ; 
       
        $groupe->setDateAjout(new \DateTime('now')) ;  
        
        $groupe->setObjectif($request->get('objectif')) ; 
       
        $user = $tokenStorage->getToken()->getUser() ; 
 
        $groupe->setUser($user) ; 
      
        $competence = $competenceRepository->findOneBy ([
         "title" => $request->get('competence'), 
         
         ]) ; 
 
       //$groupe->setCompetence($competence) ; 
        
       $file = $request->files->get('file') ;   
         
       if(!empty($file))
      { $name = rand(9999, 9999999999);
      
       $extension = $file->guessExtension();
 
       $fichier = $request->getSchemeAndHttpHost() . "/Profileimages" . '/' . $name . "." . $extension;    
      
       $file->move(
               $this->getParameter('uploads_directory'), 
               $fichier
           );
     
        $groupe->setImage($fichier);  
       
        }
         
        $em = $this->getDoctrine()->getManager() ; 
        $em->persist($groupe) ;  
        $em->flush() ;
         
                    return $this->json('goodUpdate') ; 

    }





}