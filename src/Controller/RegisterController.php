<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class RegisterController extends AbstractController
{


    #[Route('/register', name: 'register',methods:["POST"])]
    public function register(ValidatorInterface $validator ,
                             SerializerInterface $serializer,
                             Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager): JsonResponse{
        if ($this->getUser()) {
            $data = ['message' => 'You must log out to access the registration page'];
            $jsonContent = $serializer->serialize($data, 'json');
            return new JsonResponse($jsonContent, Response::HTTP_UNAUTHORIZED, ['Content-Type' => 'application/json']);
        }

        $newUser = $serializer->deserialize($request->getContent(), User::class, 'json');
        dump($newUser);

        if (!$newUser instanceof User) {
            return new JsonResponse(['error' => 'Failed to deserialize User object'], Response::HTTP_BAD_REQUEST);
        }

        $error = $validator->validate($newUser);

        if ($error->count()>0){
            return new JsonResponse($serializer->serialize($error, 'json'), Response::HTTP_BAD_REQUEST,[],true);
        }

        $getPassword = $newUser->getPassword();
        $newUser->setPassword($userPasswordHasher->hashPassword($newUser,$getPassword));
        $newUser->setRoles(['ROLE_USER']);
        $newUser->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($newUser);
        $entityManager->flush();


        return new JsonResponse(
            ['status' => 'success', 'message' => 'Your account has been created.'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );    }

}
