<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecipeType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{

    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(RecetteRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        /*Cette fonction récupère toutes les recettes */

        $recettes = $paginator->paginate(
            $repository->findAll(), /*$query, query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    #[Route('/recette/nouveau', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecipeType::class, $recette);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $recette = $form->getData();
            /*dd($ingredient);*/
            $manager->persist($recette);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été crée avec succès !'
            );

            return $this->redirectToRoute(('recipe.index'));
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recette/edition/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager, Recette $recette) : Response
    {
        $form = $this->createForm(RecipeType::class, $recette);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recette = $form->getData();
            $manager->persist($recette);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès !'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recette/suppression/{id}', name: 'recipe.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Recette $recette) : Response
    {
        $manager->remove($recette);
        $manager->flush();
        
        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succès !'
        );

        return $this->redirectToRoute('recipe.index');
    }
}
