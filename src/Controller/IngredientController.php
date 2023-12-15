<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use Doctrine\ORM\EntityManager;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        /*Cette fonction récupère tout les ingrédients */

        $ingredients = $paginator->paginate(
            $repository->findAll(), /*$query, query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    #[Route('/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            /*dd($ingredient);*/
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été crée avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager,/*IngredientRepository $repository, int $id,*/ Ingredient $ingredient /*avec l'ingrédient on recherche directement l'ingredient auquel est affilié l'id*/) : Response
    {
        // $ingredient = $repository->findOneBy(['id' => $id]); autre méthode pour chercher l'ingrédient en retrouvant l'id
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            /*dd($ingredient);*/
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/suppression/{id}', name: 'ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient) : Response
    {
        $manager->remove($ingredient);
        $manager->flush();
        
        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès !'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
