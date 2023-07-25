<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    #[Route('/profil/edit', name: 'app_edit_profil')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user =  $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Les informations de votre profil ont bien été modifiées.'
            );
            return $this->redirectToRoute('app_profil');
        }
        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
