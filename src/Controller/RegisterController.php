<?php
namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'user_register', methods: ['POST'])]
    public function register(
        Request $request,
        DocumentManager $dm,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validate the input data
        if (!isset($data['name'], $data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Invalid input data'], 400);
        }

        // Check if the user already exists
        $existingUser = $dm->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'User already exists'], 400);
        }

        // Create a new user
        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        // Save the user to the database
        $dm->persist($user);
        $dm->flush();

        return new JsonResponse(['message' => 'User registered successfully'], 201);
    }
}