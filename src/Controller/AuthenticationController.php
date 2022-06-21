<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/authentication', name: 'authentication_')]
class AuthenticationController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
          return $this->render('authentication/login.html.twig', [
                           'last_username' => $lastUsername,
                           'error'         => $error,
                            'categories' => $categories
          ]);
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $user = new User();
        $user->setActive(true);
        $user->setRoles(['ROLE_USER']);

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'empty_data' => '',
                'required' => false
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'empty_data' => '',
                'required' => false
            ])

            ->add('zipcode', TextType::class, [
                'label' => 'Code Postal',
                'empty_data' => '',
                'required' => false
            ])

            ->add('number', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['maxlength => 10'],
                'required' => false
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'empty_data' => '',
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer un compte'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $password = $user->getPassword();
            $firstname = $user->getFirstname();
            $lastname = $user->getLastName();
            $slug = $slugger->slug($lastname . ' ' . $firstname)->lower();
            $user->setSlug($slug);
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('authentication_login');
        }

        return $this->render('authentication/register.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('home');
    }
}
