<?php

namespace App\Controller;

use App\Dto\BookRequestDto;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Utils\UploadedBase64File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{

    public function __construct(
        private BookRepository $bookRepository,
        private AuthorRepository $authorRepository,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer
    )
    {
    }

    #[Route('/books', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $json = $request->getContent();
        $bookRequestDto = $this->serializer->deserialize($json, BookRequestDto::class, 'json');

        // Validation
        $violations = $this->validator->validate($bookRequestDto);
        if ($violations->count()) {
            return $this->json($violations);
        }

        // Set common data
        $book = new Book();
        $book->setTitle($bookRequestDto->title);
        $book->setDescription($bookRequestDto->description);
        $book->setPublishedAt($bookRequestDto->publishedAt);


        // Set authors
        $authors = $this->authorRepository->findBy(['id' => $bookRequestDto->authorIds]);
        if ($authors) {
            foreach ($authors as $author) {
                $book->addAuthor($author);
            }
        }

        // Set image
        $imageFile = new UploadedBase64File($bookRequestDto->imageBase64, "");
        $fileName = uniqid() . '.' . $imageFile->guessExtension();
        $imageFile->move('images', $fileName);
        $book->setImage($fileName);

        $this->bookRepository->save($book,true);

        return $this->json($book);
    }

    #[Route('/books')]
    public function getAll(Request $request): JsonResponse
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $paginator = $this->bookRepository->getPaginatorForGetAll($offset, $limit);
        return $this->json($paginator);
    }

    #[Route('/books/{id}')]
    public function getById($id): JsonResponse
    {
        $book = $this->bookRepository->find($id);
        return $this->json($book);
    }

    #[Route('/books/find/{lastName}')]
    public function search($lastName): JsonResponse
    {
        $books = $this->bookRepository->findByAuthorLastName($lastName);
        return $this->json($books);
    }
}
