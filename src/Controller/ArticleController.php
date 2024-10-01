<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleForm;
use App\Form\ModifArticleForm;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository
    ) {
    }
    #[Route('/create/article/', name: 'create_article')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = new Article();
            $article->setTitle($form->get('title')->getData());
            $article->setContent($form->get('content')->getData());
            if ($form->get('image')->getData() != null) {
                $article->setImage($form->get('image')->getData());
            }
            $article->setPrice($form->get('price')->getData());
            $article->setTva(20);
            $category = $this->categoryRepository->find($form->get('category')->getData()->getId());
            $article->setCategory($category);
            $this->articleRepository->save($article);

            return $this->redirectToRoute('article_success');
        }

        return $this->render('article/createArticle.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/success/article', name: 'article_success')]
    public function success(): Response
    {
        return $this->render('article/success.html.twig');
    }

    #[Route('/modif/article/{id}', name: 'modif_article')]
    public function modif(int $id, Request $request): Response
    {
        $article = $this->articleRepository->find($id);

        $form = $this->createForm(ArticleForm::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setTitle($form->get('title')->getData());
            $article->setContent($form->get('content')->getData());
            if ($form->get('image')->getData() != null) {
                $article->setImage($form->get('image')->getData());
            }
            $article->setPrice($form->get('price')->getData());
            $category = $this->categoryRepository->find($form->get('category')->getData()->getId());
            $article->setCategory($category);
            $this->articleRepository->save($article);

            return $this->redirectToRoute('show_article', ['id' => $id]);
        }

        return $this->render('article/modifArticle.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/article/{id}', name: 'delete_article')]
    public function delete(int $id)
    {
        $article = $this->articleRepository->find($id);
        $this->articleRepository->delete($article);
        $articles = $this->articleRepository->findAll();
        return $this->render(
            'catalog/index.html.twig',
            [
                'articles' => $articles
            ]
        );
    }

    #[Route('/{id}/article', name: 'show_article')]
    public function show(int $id): Response
    {
        return $this->render('article/show.html.twig', [
            'controller_name' => 'ItemController',
            'article' => $this->articleRepository->find($id),
        ]);
    }
}
