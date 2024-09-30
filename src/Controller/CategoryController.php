<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryForm;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }
    #[Route('/create/category', name: 'create_category')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(CategoryForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = new Category();
            $category->setName($form->get('name')->getData());
            $this->categoryRepository->save($category);

            return $this->redirectToRoute('success_category');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/success/category', name: 'success_category')]
    public function success(): Response
    {
        return $this->render('category/success.html.twig');
    }
}
