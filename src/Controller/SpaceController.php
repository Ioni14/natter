<?php

namespace App\Controller;

use App\Controller\Input\CreateSpaceInput;
use App\Controller\Input\PostMessageInput;
use App\Entity\Message;
use App\Entity\Space;
use App\Repository\MessageRepository;
use App\Repository\SpaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SpaceController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;
    private SpaceRepository $spaceRepository;
    private MessageRepository $messageRepository;
    private Security $security;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        SpaceRepository $spaceRepository,
        MessageRepository $messageRepository,
        Security $security
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->spaceRepository = $spaceRepository;
        $this->messageRepository = $messageRepository;
        $this->security = $security;
    }

    /**
     * @Route("/spaces", methods={"POST"})
     */
    public function createSpace(Request $request): Response
    {
        $user = $this->security->getUser();

        try {
            /** @var CreateSpaceInput $input */
            $input = $this->serializer->deserialize($request->getContent(), CreateSpaceInput::class, $request->getContentType());
            $input->subject = $user !== null ? $user->getUsername() : null;
        } catch (NotEncodableValueException $e) {
            throw new UnsupportedMediaTypeHttpException();
        }
        $errors = $this->validator->validate($input);
        if ($errors->count() > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), 400, [], true);
        }

        $space = new Space(null, $input->name, $input->owner);
        $this->entityManager->persist($space);
        $this->entityManager->flush();

        return new JsonResponse($this->serializer->serialize($space, 'json', ['groups' => 'read']), 201, [
            'Location' => '/spaces/' . $space->getId(),
            'Content-Type' => 'application/json;charset=utf-8',
        ], true);
    }

    /**
     * @Route("/spaces/{spaceId<\d+>}/messages", methods={"POST"})
     */
    public function postMessage(Request $request, string $spaceId): Response
    {
        /** @var Space|null $space */
        $space = $this->spaceRepository->find($spaceId);
        if ($space === null) {
            throw new NotFoundHttpException();
        }

        $user = $this->security->getUser();

        try {
            /** @var PostMessageInput $input */
            $input = $this->serializer->deserialize($request->getContent(), PostMessageInput::class, $request->getContentType());
            $input->subject = $user !== null ? $user->getUsername() : null;
        } catch (NotEncodableValueException $e) {
            throw new UnsupportedMediaTypeHttpException();
        }
        $errors = $this->validator->validate($input);
        if ($errors->count() > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), 400, [], true);
        }

        $message = new Message(null, $space, $input->author, $input->message, new \DateTimeImmutable());
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return new JsonResponse($this->serializer->serialize($message, 'json', ['groups' => 'read']), 201, [
            'Location' => '/spaces/' . $space->getId() . '/' . $message->getId(),
            'Content-Type' => 'application/json;charset=utf-8',
        ], true);
    }

    /**
     * @Route("/spaces/{spaceId<\d+>}/messages/{messageId<\d+>}", methods={"GET"})
     */
    public function readMessage(Request $request, string $spaceId, string $messageId): Response
    {
        $message = $this->messageRepository->findOneBy(['id' => $messageId, 'space' => $spaceId]);
        if ($messageId === null) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse($this->serializer->serialize($message, 'json', ['groups' => 'read']), 200, [
            'Content-Type' => 'application/json;charset=utf-8',
        ], true);
    }

    /**
     * @Route("/spaces/{spaceId<\d+>}/messages", methods={"GET"})
     */
    public function findMessages(Request $request, string $spaceId): Response
    {
        try {
            $since = new \DateTimeImmutable($request->query->get('since', '-1 day'));
        } catch (\Exception $e) {
            $since = new \DateTimeImmutable('-1 day');
        }

        $messages = $this->messageRepository->createQueryBuilder('msg')
            ->where('msg.space = :space_id AND msg.createdAt >= :created_at')
            ->setParameters([
                'space_id' => $spaceId,
                'created_at' => $since,
            ])
            ->getQuery()
            ->getResult();

        return new JsonResponse($this->serializer->serialize($messages, 'json', ['groups' => 'read']), 200, [
            'Content-Type' => 'application/json;charset=utf-8',
        ], true);
    }
}
