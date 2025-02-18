<?php

namespace App\Controller;

use App\Entity\Coach;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;


class AdminController extends AbstractController
{

    #[Route('/admin/login', name: 'admin_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return $this->json([
            'message' => 'Utilisez un email et un mot de passe valides pour obtenir un token.'
        ]);
    }


    #[Route('/admin/create-coach', name: 'admin_create_coach', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')] 
    public function createCoach(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['prenom'], $data['email'], $data['password'], $data['telephone'])) {
            return $this->json(['error' => 'Données manquantes'], Response::HTTP_BAD_REQUEST);
        }

        $existingCoach = $entityManager->getRepository(Coach::class)->findOneBy(['email' => $data['email']]);
        if ($existingCoach) {
            return $this->json(['error' => 'Cet email est déjà utilisé'], Response::HTTP_CONFLICT);
        }

        $coach = new Coach();
        $coach->setNom($data['nom']);
        $coach->setPrenom($data['prenom']);
        $coach->setEmail($data['email']);
        $coach->setTelephone($data['telephone']);

        $hashedPassword = $passwordHasher->hashPassword($coach, $data['password']);
        $coach->setPassword($hashedPassword);

        $coach->setRoles(['ROLE_COACH']);

        $entityManager->persist($coach);
        $entityManager->flush();

        return $this->json([
            'message' => 'Coach créé avec succès !',
            'coach' => [
                'id' => $coach->getId(),
                'nom' => $coach->getNom(),
                'prenom' => $coach->getPrenom(),
                'email' => $coach->getEmail(),
                'telephone' => $coach->getTelephone(),
                'roles' => $coach->getRoles(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/admin/update-role/{id}', name: 'admin_update_role', methods: ['PATCH'])]
#[IsGranted('ROLE_ADMIN')]
public function updateUserRole(
    int $id,
    Request $request,
    EntityManagerInterface $entityManager
): JsonResponse {
    $data = json_decode($request->getContent(), true);

    if (!isset($data['roles']) || !is_array($data['roles'])) {
        return $this->json(['error' => 'Le champ roles est requis et doit être un tableau'], Response::HTTP_BAD_REQUEST);
    }

    $user = $entityManager->getRepository(Coach::class)->find($id)
        ?? $entityManager->getRepository(\App\Entity\Swimmer::class)->find($id)
        ?? $entityManager->getRepository(\App\Entity\Admin::class)->find($id);

    if (!$user) {
        return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
    }

    $user->setRoles($data['roles']);
    $entityManager->persist($user);
    $entityManager->flush();

    return $this->json([
        'message' => 'Rôles mis à jour avec succès',
        'user' => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ],
    ], Response::HTTP_OK);
}
}
