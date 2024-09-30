<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleForm;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    public function __construct(private CategoryRepository $categoryRepository,
                                private ArticleRepository $articleRepository)
    {
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
            $article->setImage($form->get('image')->getData());
            $article->setPrice($form->get('price')->getData());
            $article->setTva($form->get('tva')->getData());
            if ($category = $this->categoryRepository->findBy(['name' => $form->get('category')->getData()])) {
                $article->setCategory($category);
            }
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
}