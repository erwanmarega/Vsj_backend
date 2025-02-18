<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Message;
use App\Entity\Swimmer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MessageController.php',
        ]);
    }

     
    #[Route("/api/messages/send", name:'create_message', methods:['POST'])]
    
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $sender = $this->getUser();

        if (!$sender instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $receiver = $entityManager->getRepository(Swimmer::class)->find($data['receiverId']);
        if (!$receiver) {
            return $this->json(['message' => 'Destinataire introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        if ($sender->getId() === $receiver->getId()) {
            return $this->json(['message' => 'Vous ne pouvez pas vous envoyer un message à vous-même'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $message = new Message();
        $message->setContent($data['content']);
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setSubject($data['subject'] ?? '');
        $message->setCreatedAt(new \DateTime());

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json(['message' => 'Message envoyé avec succès'], JsonResponse::HTTP_CREATED);
    }

    
      #[Route("/api/messages/received", name:'received_messages', methods:['GET'])]
     
    public function receivedMessages(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $messages = $entityManager->getRepository(Message::class)->findBy(['receiver' => $user], ['createdAt' => 'DESC']);

        return $this->json($messages, JsonResponse::HTTP_OK, [], ['groups' => 'message']);
    }

    
     #[Route("/api/messages/sent", name:'sent_messages', methods:['GET'])]
    
    public function sentMessages(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $messages = $entityManager->getRepository(Message::class)->findBy(['sender' => $user], ['createdAt' => 'DESC']);

        return $this->json($messages, JsonResponse::HTTP_OK, [], ['groups' => 'message']);
    }

    
    #[Route("/api/messages/delete/{id}", name:'delete_message', methods:['DELETE'])]
     
    public function deleteMessage(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $message = $entityManager->getRepository(Message::class)->find($id);
        if (!$message) {
            return $this->json(['message' => 'Message introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        if ($message->getSender() !== $user && $message->getReceiver() !== $user) {
            return $this->json(['message' => 'Action non autorisée'], JsonResponse::HTTP_FORBIDDEN);
        }
        
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->json(['message' => 'Message supprimé avec succès'], JsonResponse::HTTP_OK);
    }

    
     #[Route("/api/messages/contacts", name:'get_contacts', methods:['GET'])]
     
    public function getContacts(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof Swimmer) {
            return $this->json(['message' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $query = $entityManager->createQuery(
            'SELECT m
             FROM App\Entity\Message m
             WHERE (m.sender = :user OR m.receiver = :user)
             AND m.createdAt IN (
                 SELECT MAX(m2.createdAt)
                 FROM App\Entity\Message m2
                 WHERE (m2.sender = m.sender AND m2.receiver = m.receiver)
                    OR (m2.sender = m.receiver AND m2.receiver = m.sender)
                 GROUP BY m2.sender, m2.receiver
             )
             ORDER BY m.createdAt DESC'
        )->setParameter('user', $user);

        $lastMessages = $query->getResult();

        $contacts = array_map(function (Message $message) use ($user) {
            $contact = $message->getSender() === $user ? $message->getReceiver() : $message->getSender();
            return [
                'id' => $contact->getId(),
                'name' => $contact->getPrenom() . ' ' . $contact->getNom(),
                'lastMessage' => $message->getContent(),
                'date' => $message->getCreatedAt()->format(\DateTime::ATOM),
                'avatar' => '/assets/icons/Avatar03.png',
            ];
        }, $lastMessages);

        return $this->json($contacts);
    }
}


