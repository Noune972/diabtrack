<?php

namespace App\Controller;

use App\Entity\ArticleCategory;
use App\Form\ArticleCategoryType;
use App\Repository\ArticleCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/article/category')]
final class ArticleCategoryController extends AbstractController
{
    #[Route(name: 'app_article_category_index', methods: ['GET'])]
    public function index(ArticleCategoryRepository $articleCategoryRepository): Response
    {
        return $this->render('article_category/index.html.twig', [
            'article_categories' => $articleCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_article_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $articleCategory = new ArticleCategory();
        $form = $this->createForm(ArticleCategoryType::class, $articleCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($articleCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article_category/new.html.twig', [
            'article_category' => $articleCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_category_show', methods: ['GET'])]
    public function show(ArticleCategory $articleCategory): Response
    {
        return $this->render('article_category/show.html.twig', [
            'article_category' => $articleCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ArticleCategory $articleCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleCategoryType::class, $articleCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article_category/edit.html.twig', [
            'article_category' => $articleCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_category_delete', methods: ['POST'])]
    public function delete(Request $request, ArticleCategory $articleCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$articleCategory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($articleCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
