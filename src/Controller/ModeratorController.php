<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModeratorController
{
    private MessageRepository $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * @Route("/spaces/{spaceId<\d+>}/message/{messageId<\d+>}}", methods={"DELETE"})
     */
    public function deletePost(Request $request, int $spaceId, int $messageId): Response
    {
        $this->messageRepository->createQueryBuilder('msg')
            ->delete()
            ->where('msg.id = :msg_id AND msg.space = :space_id')
            ->setParameters([
                'msg_id' => $messageId,
                'space_id' => $spaceId,
            ])
            ->getQuery()
            ->execute();

        return new Response(null, 204);
    }
}
