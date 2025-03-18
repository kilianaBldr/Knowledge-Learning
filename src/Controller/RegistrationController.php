<?php

namespace App\Controller;

use App\Entity\User;
use \DateTime;
use App\Service\MailerService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Security\UserAuthenticator;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer un token unique pour la vérification de l'e-mail
            $confirmationToken = Uuid::v4()->toRfc4122();
            $user->setConfirmationToken($confirmationToken);

            // Définir l'expiration du token à 24 heures
            $user->setTokenRegistrationLifetime((new \DateTime())->add(new \DateInterval('P1D')));

            // Hasher le mot de passe
            $plainPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new \Symfony\Component\Form\FormError('Les motes de passe ne correspondent pas.'));
            } else {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
