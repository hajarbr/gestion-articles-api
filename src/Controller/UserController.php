<?php
namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractController
{
    #[Route('/api/register', name: 'user_register', methods: ['POST'])]
    public function register(
        Request $request,
        DocumentManager $dm,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $dm->persist($user);
        $dm->flush();

        return new JsonResponse(['message' => 'User registered successfully'], 201);
    }

    #[Route('/api/users', name: 'create_user', methods: ['POST'])]
    public function createUser(
        Request $request,
        DocumentManager $dm,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $dm->persist($user);
        $dm->flush();

        return new JsonResponse(['message' => 'User created successfully'], 201);
    }

    #[Route('/api/users/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(
        string $id,
        Request $request,
        DocumentManager $dm,
        ValidatorInterface $validator
    ): JsonResponse {
        $user = $dm->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
    
        $data = json_decode($request->getContent(), true);
        $user->setName($data['name'] ?? $user->getName());
        $user->setEmail($data['email'] ?? $user->getEmail());
    
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }
    
        $dm->flush();
    
        return new JsonResponse([
            'message' => 'User updated successfully',
            'user' => [
                'id' => (string) $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ],
        ], 200);
    }

    #[Route('/api/account', name: 'get_account', methods: ['GET'])]
    public function getAccount(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        return new JsonResponse([
            'id' => (string) $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ], 200);
    }
}