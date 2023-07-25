<?php

namespace App\Controller;

use App\Entity\Objectif;
use App\Form\ObjectifType;
use App\Repository\ObjectifRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/objectif')]
class ObjectifController extends AbstractController
{
    #[Route('/', name: 'app_objectif_index', methods: ['GET'])]
    public function index(ObjectifRepository $objectifRepository): Response
    {
        return $this->render('objectif/index.html.twig', [
            'objectifs' => $objectifRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_objectif_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ObjectifRepository $objectifRepository): Response
    {
        $objectif = new Objectif();
        $form = $this->createForm(ObjectifType::class, $objectif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash(
                'success',
                'Les informations de votre objectif ont bien été ajoutées.'
            );
            $objectif->setUser($this->getUser());
            $objectifRepository->save($objectif, true);
            return $this->redirectToRoute('app_board');
        }

        return $this->renderForm('objectif/new.html.twig', [
            'objectif' => $objectif,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_objectif_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Objectif $objectif, ObjectifRepository $objectifRepository): Response
    {
        $form = $this->createForm(ObjectifType::class, $objectif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash(
                'success',
                'Les informations de votre objectif ont bien été modifiées.'
            );
            $objectifRepository->save($objectif, true);
            return $this->redirectToRoute('app_board');
        }

        return $this->renderForm('objectif/edit.html.twig', [
            'objectif' => $objectif,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_objectif_delete', methods: ['POST'])]
    public function delete(Request $request, Objectif $objectif, ObjectifRepository $objectifRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $objectif->getId(), $request->request->get('_token'))) {
            $objectifRepository->remove($objectif, true);
            $this->addFlash(
                'danger',
                'Votre objectif a bien été supprimé.'
            );
        }
        return $this->redirectToRoute('app_board');
    }
}
