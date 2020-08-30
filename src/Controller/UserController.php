<?php

namespace App\Controller;

use App\Controller\Input\RegisterUserInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @Route("/users", methods={"POST"})
     */
    public function registerUser(Request $request): Response
    {
        try {
            /** @var RegisterUserInput $input */
            $input = $this->serializer->deserialize($request->getContent(), RegisterUserInput::class, $request->getContentType());
        } catch (NotEncodableValueException $e) {
            throw new UnsupportedMediaTypeHttpException();
        }
        $errors = $this->validator->validate($input);
        if ($errors->count() > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), 400, [], true);
        }

        $encryptedPassword = $this->userPasswordEncoder->encodePassword(new User(null, '', ''), $input->password);

        $user = new User(null, $input->username, $encryptedPassword);
        unset($encryptedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse($this->serializer->serialize($user, 'json', ['groups' => 'read']), 201, [
            'Location' => '/users/' . $user->getId(),
            'Content-Type' => 'application/json;charset=utf-8',
        ], true);
    }
}
