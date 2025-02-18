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
use App\Entity\Groups;
use App\Entity\Swimmer;
use App\Entity\Training;


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

#[Route('/admin/update-swimmer-group', name: 'admin_update_swimmer_group', methods: ['PATCH'])]
#[IsGranted('ROLE_ADMIN')]
public function updateSwimmerGroup(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['swimmer_id'], $data['new_discipline'], $data['new_group_name'])) {
        return $this->json(['error' => 'ID du nageur, discipline et nom du groupe requis'], Response::HTTP_BAD_REQUEST);
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

    $newDiscipline = $data['new_discipline'];
    $newGroupName = $data['new_group_name'];

    if (($newDiscipline === 'Aquabike' || $newDiscipline === 'Aquagym') && $age < 18) {
        return $this->json([
            'error' => 'Vous devez avoir au moins 18 ans pour choisir cette discipline'
        ], Response::HTTP_FORBIDDEN);
    }

    $groupRepository = $entityManager->getRepository(Groups::class);
    $newGroup = $groupRepository->findOneBy(['name' => $newGroupName, 'discipline' => $newDiscipline]);

    if (!$newGroup) {
        return $this->json(['error' => 'Le groupe spécifié n\'existe pas'], Response::HTTP_NOT_FOUND);
    }

    $currentGroup = $swimmer->getGroup();
    if ($currentGroup && $currentGroup->getName() === 'Adolescents (11 - 17 ans)' && $age >= 18) {
        $newGroup = $groupRepository->findOneBy(['name' => 'Adultes (Nageurs confirmés)', 'discipline' => 'Natation']);
    }

    $swimmer->setGroup($newGroup);
    $entityManager->persist($swimmer);
    $entityManager->flush();

    return $this->json([
        'message' => 'Le groupe du nageur a été mis à jour avec succès',
        'swimmer_id' => $swimmer->getId(),
        'new_group' => [
            'id' => $newGroup->getId(),
            'name' => $newGroup->getName(),
            'discipline' => $newGroup->getDiscipline()
        ]
    ], Response::HTTP_OK);
}

#[Route('/admin/assign-groups-to-coach', name: 'admin_assign_groups_to_coach', methods: ['POST'])]
#[IsGranted('ROLE_ADMIN')]
public function assignGroupsToCoach(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['coach_id'], $data['groups_id']) || !is_array($data['groups_id'])) {
        return $this->json(['error' => 'ID du coach et liste des groupes requis'], Response::HTTP_BAD_REQUEST);
    }

    $coach = $entityManager->getRepository(Coach::class)->find($data['coach_id']);

    if (!$coach) {
        return $this->json(['error' => 'Coach non trouvé'], Response::HTTP_NOT_FOUND);
    }

    $groupRepository = $entityManager->getRepository(Groups::class);
    $groups = $groupRepository->findBy(['id' => $data['groups_id']]);

    if (count($groups) !== count($data['groups_id'])) {
        return $this->json(['error' => 'Un ou plusieurs groupes spécifiés sont introuvables'], Response::HTTP_NOT_FOUND);
    }

    foreach ($groups as $group) {
        $coach->addGroup($group);
    }

    $entityManager->persist($coach);
    $entityManager->flush();

    return $this->json([
        'message' => 'Les groupes ont été assignés avec succès au coach',
        'coach_id' => $coach->getId(),
        'assigned_groups' => array_map(function ($group) {
            return [
                'id' => $group->getId(),
                'name' => $group->getName(),
                'discipline' => $group->getDiscipline()
            ];
        }, $groups)
    ], Response::HTTP_OK);
}

#[Route('/admin/create-training', name: 'admin_create_training', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createTraining(
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['dateTraining'], $data['durationTraining'], $data['intensityTraining'], $data['categoryTraining'], $data['groupId'])) {
            return $this->json(['error' => 'Données incomplètes'], Response::HTTP_BAD_REQUEST);
        }

        $group = $entityManager->getRepository(Groups::class)->find($data['groupId']);
        if (!$group) {
            return $this->json(['error' => 'Groupe introuvable'], Response::HTTP_NOT_FOUND);
        }

        $training = new Training();
        $training->setTitle($data['title']);
        $training->setDateTraining(new \DateTime($data['dateTraining']));
        $training->setDurationTraining($data['durationTraining']);
        $training->setIntensityTraining($data['intensityTraining']);
        $training->setCategoryTraining($data['categoryTraining']);
        $training->setDescriptionTraining($data['description'] ?? null);
        $training->setIsDefinedTraining($data['isDefinedTraining'] ?? false);
        $training->setGroup($group);

        $entityManager->persist($training);
        $entityManager->flush();

        return $this->json([
            'message' => 'Entraînement créé avec succès !',
            'training' => [
                'id' => $training->getId(),
                'title' => $training->getTitle(),
                'dateTraining' => $training->getDateTraining()->format('Y-m-d H:i:s'),
                'duration' => $training->getDurationTraining(),
                'intensity' => $training->getIntensityTraining(),
                'category' => $training->getCategoryTraining(),
                'description' => $training->getDescriptionTraining(),
                'isDefined' => $training->isIsDefinedTraining(),
                'group' => $group->getName(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/admin/trainings', name: 'get_all_trainings', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getAllTrainings(EntityManagerInterface $entityManager): JsonResponse
    {
        $trainings = $entityManager->getRepository(Training::class)->findAll();

        if (!$trainings) {
            return $this->json(['message' => 'Aucun entraînement trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = [];

        foreach ($trainings as $training) {
            $data[] = [
                'id' => $training->getId(),
                'title' => $training->getTitle(),
                'dateTraining' => $training->getDateTraining()->format('Y-m-d H:i:s'),
                'duration' => $training->getDurationTraining(),
                'intensity' => $training->getIntensityTraining(),
                'category' => $training->getCategoryTraining(),
                'description' => $training->getDescriptionTraining(),
                'isDefined' => $training->isIsDefinedTraining(),
                'group' => $training->getGroup() ? $training->getGroup()->getName() : null,
            ];
        }

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/admin/training/{id}', name: 'get_training_by_id', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getTrainingById(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $training = $entityManager->getRepository(Training::class)->find($id);

        if (!$training) {
            return $this->json(['message' => 'Entraînement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $training->getId(),
            'title' => $training->getTitle(),
            'dateTraining' => $training->getDateTraining()->format('Y-m-d H:i:s'),
            'duration' => $training->getDurationTraining(),
            'intensity' => $training->getIntensityTraining(),
            'category' => $training->getCategoryTraining(),
            'description' => $training->getDescriptionTraining(),
            'isDefined' => $training->isIsDefinedTraining(),
            'group' => $training->getGroup() ? $training->getGroup()->getName() : null,
        ];

        return $this->json($data, Response::HTTP_OK);
    }

}
