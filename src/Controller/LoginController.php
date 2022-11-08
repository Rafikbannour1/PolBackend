<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser ; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LoginController extends AbstractController
{
    /**
     * @Route("/ap/login", name="api_login",methods="POST")
     */
    public function index(TokenStorageInterface $tokenStorage): Response
    {
        
        $user = $tokenStorage->getToken() ; 
        if (null === $user) {
                         return $this->json([
                            'message' => 'missing credentials',
                        ], Response::HTTP_UNAUTHORIZED);
            }
            
          
          
            $token = $tokenStorage->getToken(); 
           
            return $this->json([
               
                            'user'  => $user,
                            'token' => $token, 
                          ]);

    }
}
