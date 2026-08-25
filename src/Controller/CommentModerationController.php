<?php

namespace App\Controller;

use App\Entity\CommentArticle;
use App\Enum\CommentStatus;
use App\Repository\CommentArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/blog/admin/comments')]
#[IsGranted('ROLE_ADMIN')]
final class CommentModerationController extends AbstractController
{
    #[Route(name: 'app_comment_moderation_index', methods: ['GET'])]
    public function index(CommentArticleRepository $commentArticleRepository): Response
    {
        return $this->render('comment_moderation/index.html.twig', [
            'comments' => $commentArticleRepository->findEnAttente(),
        ]);
    }

    #[Route('/{id}/validate', name: 'app_comment_moderation_validate', methods: ['POST'])]
    public function validate(Request $request, CommentArticle $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('validate'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $comment->setStatus(CommentStatus::VALID);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire validé.');
        }

        return $this->redirectToRoute('app_comment_moderation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reject', name: 'app_comment_moderation_reject', methods: ['POST'])]
    public function reject(Request $request, CommentArticle $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reject'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            // On supprime le commentaire rejeté plutôt que de le laisser en NON_VALID indéfiniment.
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire rejeté et supprimé.');
        }

        return $this->redirectToRoute('app_comment_moderation_index', [], Response::HTTP_SEE_OTHER);
    }
}