<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IngredientController extends AbstractController
{
    /** 
     * this function is used to display the list of ingredients with pagination.
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */  
    #[Route('/ingredient', name: 'app_ingredient', methods:['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator,Request $request): Response
    {  
        $ingredients = $paginator->paginate(
        $repository->findAll(), 
        $request->query->getInt('page', 1),
        10 
    );

        return $this->render('pages/ingredient/index.html.twig',[
            'ingredients' => $ingredients
        ]);
    }  
    
    /** 
     * this function is used to add their ingredients.
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */  

    #[Route('/ingredient/new', name: 'app_ingredient_new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {  
        $ingredient = new Ingredient(); 
        $form = $this->createForm(IngredientType::class, $ingredient);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            $ingredient = $form->getData();  
            $em->persist($ingredient); 
            $em->flush();   
            
            $this->addFlash('success', 'Votre ingredient a été ajouté avec succės');
            return $this->redirectToRoute('app_ingredient');   
        }
        return $this->render('pages/ingredient/new.html.twig',[
            'form'=> $form->createView()
        ]);   

    }  
    
    #[Route('/ingredient/edit/{id}', name:'app_ingredient_edit', methods:['GET', 'POST'])]
    public function edit(Request $request , EntityManagerInterface $em, Ingredient $ingredient): Response
    { 
        $form = $this->createForm(IngredientType::class, $ingredient);  
        $ingredient = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData(); 
            $em->flush(); 

            $this->addFlash('sucess', 'votre produit a ete modifié avec succes');
            return $this->redirectToRoute('app_ingredient');
        } 
        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/delete/{id}',name:'app_ingredient_delete', methods:['GET'])] 
    public function delete(Ingredient $ingredient, EntityManagerInterface $manager) : Response
    {    
        if (!$ingredient) {
            $this->addFlash('success', 'cet ingrédient est introuvable');
            return $this->redirectToRoute('app_ingredient'); 
        } 

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash('success', 'cet ingrédient a été supprimé avec succes'); 
            return $this->redirectToRoute('app_ingredient'); 

    }
}
