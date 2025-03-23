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
use App\Entity\Attendance;
use App\Entity\Training;
use App\Entity\Message;
use Symfony\Component\Security\Http\Attribute\IsGranted;


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


    
     #[Route("/api/swimmer/me", name:'get_swimmer_info', methods:['GET'])]
    public function getSwimmerInfo(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'bio' => $user->getBio(),
            'dateNaissance' => $user->getDateNaissance()?->format('Y-m-d'),
            'adresse' => $user->getAdresse(),
            'codePostal' => $user->getCodePostal(),
            'ville' => $user->getVille(),
            'telephone' => $user->getTelephone(),
            'roles' => $user->getRoles(),
            'groupId' => $user->getGroup() ? $user->getGroup()->getId() : null, 
        'groupName' => $user->getGroup() ? $user->getGroup()->getName() : null, 
        ], JsonResponse::HTTP_OK);
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

    #[Route('/swimmer/attendance', name: 'swimmer_attendance', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function markAttendance(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['trainingId'], $data['isAttendance'])) {
            return $this->json(['error' => 'Données incomplètes'], Response::HTTP_BAD_REQUEST);
        }

        $swimmer = $this->getUser();

        $training = $entityManager->getRepository(Training::class)->find($data['trainingId']);
        if (!$training) {
            return $this->json(['error' => 'Entraînement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $existingAttendance = $entityManager->getRepository(Attendance::class)->findOneBy([
            'swimmer' => $swimmer,
            'training' => $training
        ]);

        if ($existingAttendance) {
            $existingAttendance->setAttendance($data['isAttendance']);
        } else {
            $attendance = new Attendance();
            $attendance->setSwimmer($swimmer);
            $attendance->setTraining($training);
            $attendance->setAttendance($data['isAttendance']);

            $entityManager->persist($attendance);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Présence mise à jour avec succès',
            'training' => [
                'id' => $training->getId(),
                'title' => $training->getTitle()
            ],
            'attendance' => $data['isAttendance'] ? "Présent" : "Absent"
        ], Response::HTTP_OK);
    }

    #[Route('/swimmer/attendances', name: 'get_swimmer_attendances', methods: ['GET'])]
#[IsGranted('ROLE_USER')]
public function getSwimmerAttendances(EntityManagerInterface $entityManager): JsonResponse
{
    $swimmer = $this->getUser();
    $attendances = $entityManager->getRepository(Attendance::class)->findBy(['swimmer' => $swimmer]);

    if (!$attendances) {
        return $this->json(['message' => 'Aucune présence enregistrée'], Response::HTTP_NOT_FOUND);
    }

    $data = [];
    foreach ($attendances as $attendance) {
        $data[] = [
            'trainingId' => $attendance->getTraining()->getId(),
            'trainingTitle' => $attendance->getTraining()->getTitle(),
            'isAttendance' => $attendance->isAttendance() ? "Présent" : "Absent"
        ];
    }

    return $this->json($data, Response::HTTP_OK);
}

#[Route('/api/swimmer/bio', name: 'update_swimmer_bio', methods: ['POST'])]
public function updateSwimmerBio(Request $request): JsonResponse
{
    $user = $this->getUser();

    if (!$user instanceof Swimmer) {
        return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    $data = json_decode($request->getContent(), true);

    if (empty($data['bio'])) {
        return $this->json(['message' => 'La bio est requise'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $user->setBio($data['bio']);
    $this->entityManager->persist($user);
    $this->entityManager->flush();

    return $this->json([
        'message' => 'Bio mise à jour avec succès',
        'bio' => $user->getBio(),
    ], JsonResponse::HTTP_OK);
}

#[Route('/api/group/{groupId}/trainings', name: 'get_group_trainings', methods: ['GET'])]
public function getGroupTrainings(int $groupId, Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $group = $entityManager->getRepository(Groups::class)->find($groupId);

    if (!$group) {
        return $this->json(['error' => 'Groupe non trouvé'], Response::HTTP_NOT_FOUND);
    }

    $startOfMonth = new \DateTime('first day of this month 00:00:00');
    $endOfMonth = new \DateTime('last day of this month 23:59:59');

    $month = $request->query->get('month'); 
    if ($month) {
        $startOfMonth = new \DateTime("first day of $month");
        $endOfMonth = new \DateTime("last day of $month 23:59:59");
    }

    $trainings = $entityManager->getRepository(Training::class)->createQueryBuilder('t')
        ->where('t.group = :group')
        ->andWhere('t.dateTraining BETWEEN :start AND :end')
        ->setParameter('group', $group)
        ->setParameter('start', $startOfMonth)
        ->setParameter('end', $endOfMonth)
        ->orderBy('t.dateTraining', 'ASC')
        ->getQuery()
        ->getResult();

    $trainingData = array_map(function ($training) {
        return [
            'id' => $training->getId(),
            'title' => $training->getTitle(),
            'date' => $training->getDateTraining()->format('Y-m-d H:i'),
            'duration' => $training->getDurationTraining(),
            'intensity' => $training->getIntensityTraining(),
            'category' => $training->getCategoryTraining(),
            'description' => $training->getDescriptionTraining(),
        ];
    }, $trainings);

    return $this->json([
        'message' => 'Entraînements récupérés avec succès',
        'group' => $group->getName(),
        'trainings' => $trainingData
    ], Response::HTTP_OK);
}

#[Route("/api/swimmer/conversations", name: "get_swimmer_conversations", methods: ["GET"])]
public function getSwimmerConversations(EntityManagerInterface $entityManager): JsonResponse
{
    $user = $this->getUser();

    if (!$user instanceof Swimmer) {
        return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    $query = $entityManager->createQuery(
        'SELECT m
         FROM App\Entity\Message m
         WHERE m.createdAt = (
             SELECT MAX(m2.createdAt)
             FROM App\Entity\Message m2
             WHERE (m2.sender = m.sender AND m2.receiver = m.receiver)
                OR (m2.sender = m.receiver AND m2.receiver = m.sender)
         )
         AND (m.sender = :user OR m.receiver = :user)
         ORDER BY m.createdAt DESC'
    )->setParameter('user', $user);

    $lastMessages = $query->getResult();
    
    $conversations = [];
    
    foreach ($lastMessages as $message) {
        $contact = $message->getSender() === $user ? $message->getReceiver() : $message->getSender();
        $contactId = $contact->getId();

        if (!isset($conversations[$contactId])) {
            $prenom = $contact->getPrenom() ?? '';
            $nom = $contact->getNom() ?? '';
            $name = trim($prenom . ' ' . $nom) ?: $contact->getEmail(); 

            $conversations[$contactId] = [
                'id' => $contactId,
                'name' => $name,
                'lastMessage' => $message->getContent(),
                'date' => $message->getCreatedAt()->format(\DateTime::ATOM),
                'avatar' => '/assets/icons/Avatar03.png',
            ];
        }
    }

    return $this->json(array_values($conversations));
}


    #[Route("/api/messages", name: "get_messages", methods: ["GET"])]
    public function getMessages(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $recipientId = $request->query->get('recipientId');

        if (!$user instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$recipientId) {
            return $this->json(['message' => 'ID du destinataire requis'], Response::HTTP_BAD_REQUEST);
        }

        $recipient = $entityManager->getRepository(Swimmer::class)->find($recipientId);
        if (!$recipient) {
            return $this->json(['message' => 'Destinataire non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $messages = $entityManager->getRepository(Message::class)->findBy(
            [
                'sender' => [$user, $recipient],
                'receiver' => [$user, $recipient]
            ],
            ['createdAt' => 'ASC']
        );

        $data = array_map(function (Message $msg) use ($user) {
            return [
                'id' => $msg->getId(),
                'senderId' => $msg->getSender()->getId(),
                'content' => $msg->getContent(),
                'timestamp' => $msg->getCreatedAt()->format(\DateTime::ATOM),
                'isMine' => $msg->getSender() === $user,
            ];
        }, $messages);

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route("/api/messages", name: "send_message", methods: ["POST"])]
public function sendMessage(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    try {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $recipientId = $data['recipientId'] ?? null;
        $content = $data['content'] ?? null;

        if (!$user instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$recipientId || !$content) {
            return $this->json(['message' => 'ID du destinataire et contenu requis'], Response::HTTP_BAD_REQUEST);
        }

        $recipient = $entityManager->getRepository(Swimmer::class)->find($recipientId);
        if (!$recipient) {
            return $this->json(['message' => 'Destinataire non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $message = new Message();
        $message->setSender($user);
        $message->setReceiver($recipient);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTime());

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([
            'id' => $message->getId(),
            'senderId' => $user->getId(),
            'content' => $message->getContent(),
            'timestamp' => $message->getCreatedAt()->format(\DateTime::ATOM),
            'isMine' => true,
        ], Response::HTTP_CREATED);
    } catch (\Exception $e) {
        return $this->json(
            ['message' => 'Erreur interne du serveur', 'details' => $e->getMessage()],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
}
