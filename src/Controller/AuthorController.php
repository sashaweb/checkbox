<?php

namespace App\Controller;

use App\Dto\AuthorRequestDto;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorController extends AbstractController
{
    public function __construct(
        private AuthorRepository $authorRepository,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer
    )
    {
    }


    #[Route('/authors', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $authorRequestDto = $this->serializer->deserialize($request->getContent(), AuthorRequestDto::class, 'json');

        $violations = $this->validator->validate($authorRequestDto);
        if ($violations->count()) {
            return $this->json($violations);
        }

        $author = new Author();
        $author->setFirstName($authorRequestDto->firstName);
        $author->setLastName($authorRequestDto->lastName);
        $author->setPatronymic($authorRequestDto->patronymic);

        $this->authorRepository->save($author,true);

        return $this->json($author);
    }

    #[Route('/authors')]
    public function getAll(Request $request): JsonResponse
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $paginator = $this->authorRepository->getPaginatorForGetAll($offset, $limit);
        return $this->json($paginator);
    }

}
