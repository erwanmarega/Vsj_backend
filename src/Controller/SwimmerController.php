<?php

namespace App\Controller;

use App\Entity\Swimmer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Groups;


class SwimmerController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $jwtManager;
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager, 
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->security = $security;
    }

    #[Route('/register', name: 'register_swimmer', methods: ['POST'])]
    public function register(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['message' => 'L\'email et le mot de passe sont requis'], Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['message' => 'Format d\'email invalide'], Response::HTTP_BAD_REQUEST);
        }

        $existingSwimmer = $this->entityManager->getRepository(Swimmer::class)->findOneBy(['email' => $data['email']]);
        if ($existingSwimmer) {
            return $this->json(['message' => 'Cet email est déjà utilisé'], Response::HTTP_CONFLICT);
        }

        $swimmer = new Swimmer();
        $swimmer->setEmail($data['email']);

        $hashedPassword = $this->passwordHasher->hashPassword($swimmer, $data['password']);
        $swimmer->setPassword($hashedPassword);

        $swimmer->setRoles(['ROLE_USER']);

        $this->entityManager->persist($swimmer);
        $this->entityManager->flush();

        $token = $this->jwtManager->create($swimmer);

        return $this->json([
            'message' => 'Utilisateur enregistré avec succès',
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/complete-registration', name: 'complete_registration', methods: ['POST'])]
    public function completeRegistration(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom']) || empty($data['prenom'])) {
            return $this->json(['message' => 'Le nom et le prénom sont requis'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser(); 

        if (!$user instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setDateNaissance(new \DateTime($data['dateNaissance'] ?? 'now'));
        $user->setAdresse($data['adresse'] ?? null);
        $user->setCodePostal($data['codePostal'] ?? null);
        $user->setVille($data['ville'] ?? null);
        $user->setTelephone($data['telephone'] ?? null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Inscription finalisée',
        ], Response::HTTP_OK);
    }
    #[Route('/swimmer/login', name: 'login_swimmer', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['message' => 'L\'email et le mot de passe sont requis'], Response::HTTP_BAD_REQUEST);
        }

        $swimmer = $this->entityManager->getRepository(Swimmer::class)->findOneBy(['email' => $data['email']]);

        if (!$swimmer) {
            return $this->json(['message' => 'Email ou mot de passe invalide'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->passwordHasher->isPasswordValid($swimmer, $data['password'])) {
            return $this->json(['message' => 'Email ou mot de passe invalide'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($swimmer);

        return $this->json([
            'message' => 'Connexion réussie',
            'token' => $token,
        ]);
    }

    #[Route('/swimmer/change-password', name: 'change_password', methods: ['POST'])]
    public function changePassword(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['oldPassword']) || empty($data['newPassword']) || empty($data['confirmNewPassword'])) {
            return $this->json(['message' => 'L\'ancien mot de passe, le nouveau mot de passe et la confirmation sont requis'], Response::HTTP_BAD_REQUEST);
        }

        if ($data['newPassword'] !== $data['confirmNewPassword']) {
            return $this->json(['message' => 'Les nouveaux mots de passe ne correspondent pas'], Response::HTTP_BAD_REQUEST);
        }

        $swimmer = $this->security->getUser();

        if (!$swimmer instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->passwordHasher->isPasswordValid($swimmer, $data['oldPassword'])) {
            return $this->json(['message' => 'Ancien mot de passe incorrect'], Response::HTTP_UNAUTHORIZED);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($swimmer, $data['newPassword']);
        $swimmer->setPassword($hashedPassword);

        $this->entityManager->persist($swimmer);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Mot de passe modifié avec succès',
        ], Response::HTTP_OK);
    }

    #[Route('/swimmer/assign-group', name: 'assign_swimmer_group', methods: ['POST'])]
    public function assignSwimmerToGroup(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (!isset($data['swimmer_id'], $data['discipline'])) {
            return $this->json(['error' => 'ID du nageur et discipline requis'], Response::HTTP_BAD_REQUEST);
        }
    
        $swimmer = $entityManager->getRepository(Swimmer::class)->find($data['swimmer_id']);
    
        if (!$swimmer) {
            return $this->json(['error' => 'Nageur non trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        if (!$swimmer->getDateNaissance()) {
            return $this->json(['error' => 'Le nageur n\'a pas de date de naissance'], Response::HTTP_BAD_REQUEST);
        }
    
        $now = new \DateTime();
        $age = $now->diff($swimmer->getDateNaissance())->y;
        
        $selectedDiscipline = $data['discipline'];
    
        if (($selectedDiscipline === 'Aquabike' || $selectedDiscipline === 'Aquagym') && $age < 18) {
            return $this->json([
                'error' => 'Vous devez avoir au moins 18 ans pour choisir cette discipline'
            ], Response::HTTP_FORBIDDEN);
        }
    
        $groupRepository = $entityManager->getRepository(Groups::class);
        $group = null;
    
        if ($selectedDiscipline === 'Natation') {
            if ($age <= 3) {
                $group = $groupRepository->findOneBy(['name' => 'Bébé nageur (3 mois - 3 ans)', 'discipline' => 'Natation']);
            } elseif ($age <= 4) {
                $group = $groupRepository->findOneBy(['name' => 'Jardin aquatique (3 ans - 4 ans)', 'discipline' => 'Natation']);
            } elseif ($age <= 10) {
                $group = $groupRepository->findOneBy(['name' => 'Enfants (5 ans - 10 ans)', 'discipline' => 'Natation']);
            } elseif ($age <= 17) {
                $group = $groupRepository->findOneBy(['name' => 'Adolescents (11 - 17 ans)', 'discipline' => 'Natation']);
            } else {
                $group = $groupRepository->findOneBy(['name' => 'Adultes (Nageurs confirmés)', 'discipline' => 'Natation']);
            }
        } elseif ($selectedDiscipline === 'Aquabike') {
            $group = $groupRepository->findOneBy(['name' => 'Aquabike (Adultes uniquement)', 'discipline' => 'Aquabike']);
        } elseif ($selectedDiscipline === 'Aquagym') {
            $group = $groupRepository->findOneBy(['name' => 'Aquagym (Adultes uniquement)', 'discipline' => 'Aquagym']);
        }
    
        if (!$group) {
            return $this->json(['error' => 'Aucun groupe trouvé pour cette discipline'], Response::HTTP_NOT_FOUND);
        }
    
        $swimmer->setGroup($group);
        $entityManager->persist($swimmer);
        $entityManager->flush();
    
        return $this->json([
            'message' => 'Nageur assigné au groupe avec succès',
            'swimmer_id' => $swimmer->getId(),
            'group' => $group->getName(),
            'discipline' => $group->getDiscipline(),
        ], Response::HTTP_OK);
    }
    
}
