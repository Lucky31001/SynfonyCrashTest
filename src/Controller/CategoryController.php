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

    #[Route('/list/category', name: 'list_category')]
    public function list(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('category/list.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/delete/category/{id}', name: 'delete_category')]
    public function delete(int $id): Response
    {
        $category = $this->categoryRepository->find($id);
        $this->categoryRepository->delete($category);

        return $this->redirectToRoute('list_category');
    }

    #[Route('/modif/category/{id}', name: 'modif_category')]
    public function modif(int $id, Request $request): Response
    {
        $category = $this->categoryRepository->find($id);

        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName($form->get('name')->getData());
            $this->categoryRepository->save($category);

            return $this->redirectToRoute('list_category');
        }

        return $this->render('category/modif.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
