<?php

namespace App\Controller;

use App\Repository\DeathNoteRepository;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class QuoteController extends AbstractController
{

    private Serializer $serializer;

    public function construct()

    {

        $defaultContext = [  //дефолтні параметри нормалайзера
            AbstractNormalizer::GROUPS             => ['user'], //виводити з анотацією групи 'user'
            AbstractNormalizer::IGNORED_ATTRIBUTES => [   //ігнорувати дані атрибути
                '__initializer', 'isInitialized',
                'cloner'
            ]
        ];
        $encoders = [new JsonEncoder()];   //енкодер: "Я родився"
        $normalizer = new ObjectNormalizer(   //створення нормалайзера з дефолтрими настройками
            null, null, null,
            null, null, null,
            $defaultContext
        );
        $normalizers = [$normalizer];   //це хз для чого
        $this->serializer = new Serializer($normalizers, $encoders);  //створення серіалайзера (він некоректно працює)
    }

    #[Route('/', name: 'index')]   //при запуску сервера з таким роутом виконати наступну функцію
    public function index(
        QuoteRepository $quoteRepository,   //таблиця з цитатами
        DeathNoteRepository $noteRepository     //таблиця з авторами
    ): Response
    {

        return $this->render(   //відобразити в браузері сторінку з параметрами 'quotes' i 'persons'
            'index.html.twig',
            [
                'quotes'  => $quoteRepository->findAll(),   //findAll повертає масив з усіх обєктів в в таблиці
                'persons' => $noteRepository->findAll(),
            ]
        );
    }

    #[Route('/api/quote/', name: 'index1')]
    public function index1(
        SerializerInterface $serializer,   //серіалайзер (нормальний)
        QuoteRepository $quoteRepository,
        DeathNoteRepository $noteRepository
    ): Response
    {
        $this->association($quoteRepository->findAll(), $noteRepository->findAll());    //функція наводиць звязки між таблицями
        $response = $serializer->serialize($noteRepository->findAll(), JsonEncoder::FORMAT, [   //змінна, яка
            AbstractNormalizer::GROUPS => ['authors']   //містить серіалізовані обєкти з таблиці авторів з властивостями
        ]);                             //які позначені групою 'authors'  (тобто робить json)

        return new Response(   //виводить json на сторінку
            $response, Response::HTTP_OK, ['Content-type' => 'application/json']
        );
    }

    #[Route('/api/quote1/', name: 'index2')]
    public function index2(
        SerializerInterface $serializer,
        QuoteRepository $quoteRepository,
        DeathNoteRepository $noteRepository
    ): Response
    {
        //var_dump(count($noteRepository->findAll()));  //більше не працює, бо в обєктах таблиць безкінечні рекурсивні посилання, які ламають браузер
        $this->association($quoteRepository->findAll(), $noteRepository->findAll());

        $response1 = $serializer->serialize($quoteRepository->findAll(), JsonEncoder::FORMAT, [
            AbstractNormalizer::GROUPS => ['quotes']
        ]);


        /*$quoteRepository->findAll() */

        return new Response(
            $response1, Response::HTTP_OK
        );
    }
    public function association(array $table1, array $table2)
    {
        $counter = 0;
        foreach ($table1 as $quoteObj)
        {
            $table2[$counter % count($table2)]->addQuote($quoteObj);
            $counter ++;
        }
    }
}