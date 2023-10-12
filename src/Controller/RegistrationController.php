<?php

namespace App\Controller;

use App\Entity\Usr;
use App\Form\RegistrationFormType;
use App\Service\SendMailService;
use App\Security\UserAuthenticator;
use App\Repository\UsrRepository;
use App\Service\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        /**
         * Registering a new user
         */
        $user = new Usr();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // Set the user parameters
            $user->setIsVerified(false);
            $user->setRoles(['ROLE_USER']);
            if($user->getEmail() === "admin@sonway.fr") {
                $user->setRoles(['ROLE_ADMIN']);
            }

            // Save the user in the database
            $entityManager->persist($user);
            $entityManager->flush();


            // Send the verification email

            // Header of the token
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            // Payload of the token
            $payload = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'isVerified' => $user->isIsVerified()
            ];

            // Generate the token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // generate a signed url and email it to the user
            $mail->send('no-reply@sonway.fr', $user->getEmail(), 'Activation of your GamerZ account', 'register', ['user' => $user, 'token' => $token]);



            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/verify/{token}', name: 'app_verify')]
    public function verifyUser($token, JWTService $jwt, UsrRepository $userRepository, EntityManagerInterface $em): Response{
        /**
         * Verifying a user
         * @param $token The token of the user
         * @param $jwt The JWT service
         * @param $userRepository The user repository
         * @param $em The entity manager
         * return a Response
         */
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            $payload = $jwt->getPayload($token);
            $user = $userRepository->find($payload['id']);
            if($user && !$user->isIsVerified()){
                $user->setIsVerified(true);
                $em->flush();
                $this->addFlash('success', 'Your account has been activated');
                return $this->redirectToRoute('app_login');

            }else{
                $this->addFlash('error', 'Your account has already been activated');
            }
        }
        $this->addFlash('error', 'The token is invalid');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/resend', name: 'app_resend')]
    public function resend(JWTService $jwt, SendMailService $mail, UsrRepository $userRepository): Response{
        $user = $this->getUser();
        if(!$user){
            $this->addFlash('error', 'You must be logged in to resend the activation email');
            return $this->redirectToRoute('app_login');
        }
        if($user->isIsVerified()){
            $this->addFlash('error', 'Your account has already been activated');
            return $this->redirectToRoute('app_login');
        }
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'isVerified' => $user->isIsVerified()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // generate a signed url and email it to the user
        $mail->send('no-reply@sonway.fr', $user->getEmail(), 'Activation of your GamerZ account', 'register', ['user' => $user, 'token' => $token]);

        $this->addFlash('success', 'The activation email has been resent');
        return $this->redirectToRoute('app_login');

    }


}
