<?php

namespace App\Controller;

use App\Entity\Lessons;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LessonController extends AbstractController
{
    #[Route('/lesson/{id}', name: 'app_lesson_show')]
    public function show(Lessons $lesson): Response
    {
        // Vérifie si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette leçon.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifie si l'utilisateur a bien acheté la leçon
        if (!$this->getUser()->getPurchasedLessons()->contains($lesson)) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette leçon.');
            return $this->redirectToRoute('app_cursus_show', ['id' => $lesson->getCursus()->getId()]);
        }

        return $this->render('lesson/lesson.html.twig', [
            'lesson' => $lesson,
        ]);
    }
}