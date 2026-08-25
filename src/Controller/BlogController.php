<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\CommentArticle;
use App\Form\ArticleType;
use App\Form\CommentArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/blog')]
final class BlogController extends AbstractController
{
    // Accessible à tout le monde, y compris les visiteurs non connectés.
    #[Route(name: 'app_blog_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'articles' => $articleRepository->findPublies(),
        ]);
    }

    // Accessible à tout le monde. Le formulaire de commentaire n'est traité
    // que si l'utilisateur est connecté (vérifié avant handleRequest).
    #[Route('/{id}', name: 'app_blog_show', methods: ['GET', 'POST'])]
    public function show(
        Article $article,
        Request $request,
        CommentArticleRepository $commentArticleRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = new CommentArticle();
        $commentForm = $this->createForm(CommentArticleType::class, $comment);

        if ($this->getUser()) {
            $commentForm->handleRequest($request);

            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                $comment->setArticle($article);
                $comment->setPatient($this->getUser());
                $comment->setDate(new \DateTime());
                $comment->setHour(new \DateTime());
                // Le statut par défaut de l'entité est déjà NON_VALID :
                // le commentaire n'apparaîtra qu'après validation par un admin.

                $entityManager->persist($comment);
                $entityManager->flush();

                $this->addFlash('success', "Votre commentaire a bien été envoyé et sera visible après validation par un administrateur.");

                return $this->redirectToRoute('app_blog_show', ['id' => $article->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'comments' => $commentArticleRepository->findValidesPourArticle($article),
            'commentForm' => $commentForm,
        ]);
    }

    // Réservé à l'administrateur.
    #[Route('/admin/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setAuthor($this->getUser()->getUserIdentifier());

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé.');

            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    // Réservé à l'administrateur.
    #[Route('/admin/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Article modifié.');

            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    // Réservé à l'administrateur.
    #[Route('/admin/{id}/delete', name: 'app_blog_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article supprimé.');
        }

        return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
    }
}