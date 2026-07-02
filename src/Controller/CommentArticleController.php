<?php

namespace App\Controller;

use App\Entity\CommentArticle;
use App\Form\CommentArticleType;
use App\Repository\CommentArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment/article')]
final class CommentArticleController extends AbstractController
{
    #[Route(name: 'app_comment_article_index', methods: ['GET'])]
    public function index(CommentArticleRepository $commentArticleRepository): Response
    {
        return $this->render('comment_article/index.html.twig', [
            'comment_articles' => $commentArticleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_comment_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentArticle = new CommentArticle();
        $form = $this->createForm(CommentArticleType::class, $commentArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commentArticle);
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment_article/new.html.twig', [
            'comment_article' => $commentArticle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_article_show', methods: ['GET'])]
    public function show(CommentArticle $commentArticle): Response
    {
        return $this->render('comment_article/show.html.twig', [
            'comment_article' => $commentArticle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CommentArticle $commentArticle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentArticleType::class, $commentArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment_article/edit.html.twig', [
            'comment_article' => $commentArticle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_article_delete', methods: ['POST'])]
    public function delete(Request $request, CommentArticle $commentArticle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentArticle->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($commentArticle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
