<?php

namespace App\Controller\Registration;

use App\Entity\User;
use DateTimeImmutable;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private EntityManagerInterface $entityManager;


    public function __construct(EmailVerifier $emailVerifier,  EntityManagerInterface $entityManager)
    {
        $this->emailVerifier = $emailVerifier;
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'visitor_registration_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        // Si l'utilisateur est déjà connecté,
        // il n'a plus rien à faire sur la page de connexion.
        //Redirigeons-le vers la page d'accueil.
        if ($this->getUser()) {
            return $this->redirectToRoute('visitor_welcome_index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


        //encodage du mot de passe
        $passwordHashed = $userPasswordHasher->hashPassword($user,  $form->get('password')->getData());

         //initialisation de la propriété password avec le mot de passe encodé
        $user->setPassword($passwordHashed);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setUpdatedAt(new DateTimeImmutable());

        //insertions des données en base
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // 10-envoie email de vérification du compte a l'utilisateur
        $this->emailVerifier->sendEmailConfirmation(
            'visitor_registration_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('mailer@horizon.com', 'Horizon'))
                ->to($user->getEmail())
                ->subject('validation de votre compte sur le site Horizon.')
                ->htmlTemplate('pages/visitor/registration/confirmation_email.html.twig')
            );

        // 11-rediriger l'utilisateur vers la page d'attente
        return $this->redirectToRoute('visitor_registration_waiting_for_email_verification');
        }

        return $this->render('pages/visitor/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/Attente', name: 'visitor_registration_waiting_for_email_verification', methods: ["GET"])]
    public function waitingForEmailVerification(): Response
    {
        return $this->render('pages/visitor/registration/waiting_for_email_verification.html.twig');
    }

    #[Route('/verify/email', name: 'visitor_registration_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('visitor_registration_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('visitor_registration_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('visitor_registration_register');
        }

        $user->setVerifiedAt(new DateTimeImmutable());
        $user->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', "l'email a été vérifié vous pouvez vous connecter.");

        return $this->redirectToRoute('visitor_welcome_index');
    }
}
