<?php

namespace App\Controller;

use App\Entity\HistoriqueAchat;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HistoriqueAchatRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HistoriqueAchatController extends AbstractController
{
    /**
     * @Route("/pol/historique/getAll", name="historique_achat",methods={"GET"})
     */
    public function index(TokenStorageInterface $tokenStorage,GroupeRepository $courRepository,HistoriqueAchatRepository $historyRepository): Response
    {   
     
       $hitory  = $historyRepository->findAll() ; 
       $historyArray = [] ; 
       
        foreach($hitory as $oneHistory)
            {
                $historyArray [] = $oneHistory->toArray() ; 
            }
      
        return $this->json($historyArray)  ;      
       

    }

    /**
     * @Route("/pol/historique/delete/{id}", name="competence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager,$id): Response
    {
        $data = $this->getDoctrine()->getRepository(HistoriqueAchat::class)->find($id) ; 
        $em = $this->getDoctrine()->getManager() ; 
        $em->remove($data) ;
        $em->flush() ; 

        return $this->json('Deleted successfully') ;  
    }


}
