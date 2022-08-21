<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Repository\DeathNoteRepository;
use App\Repository\QuoteRepository;
use App\Form\QuoteType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
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

    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
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
        ManagerRegistry $doctrine,
        QuoteRepository $quoteRepository,
        DeathNoteRepository $noteRepository
    ): Response
    {
        $this->association($quoteRepository->findAll(), $noteRepository->findAll());    //функція наводиць звязки між таблицями
        $entityManager = $doctrine->getManager();
        foreach ($quoteRepository->findAll() as $quote)
        {
            $entityManager->persist($quote);
        }
        foreach ($noteRepository->findAll() as $note)
        {
            $entityManager->persist($note);
        }
        $entityManager->flush();

        $response = $this->serializer->serialize(
            $noteRepository->findAll(),
            JsonEncoder::FORMAT,
            [   //змінна, яка
                AbstractNormalizer::GROUPS => ['authors']   //містить серіалізовані обєкти з таблиці авторів з властивостями
            ]
        );                             //які позначені групою 'authors'  (тобто робить json)

        return new Response(   //виводить json на сторінку
            $response, Response::HTTP_OK, ['Content-type' => 'application/json']
        );
    }

    #[Route('/api/quote1/', name: 'index2')]
    public function index2(
        ManagerRegistry $doctrine,
        QuoteRepository $quoteRepository,
        DeathNoteRepository $noteRepository
    ): Response
    {
        //var_dump(count($noteRepository->findAll()));  //більше не працює, бо в обєктах таблиць безкінечні рекурсивні посилання, які ламають браузер
        $this->association($quoteRepository->findAll(), $noteRepository->findAll());

        $entityManager = $doctrine->getManager();
        foreach ($quoteRepository->findAll() as $quote)
        {
            $entityManager->persist($quote);
        }
        foreach ($noteRepository->findAll() as $note)
        {
            $entityManager->persist($note);
        }
        $entityManager->flush();

        $response1 = $this->serializer->serialize($quoteRepository->findAll(), JsonEncoder::FORMAT, [
            AbstractNormalizer::GROUPS => ['quotes']
        ]);

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

    #[Route('/api/newQuote/', name: 'new')]
    public function new(
        QuoteRepository $quoteRepository,
        Request $request): Response
    {
        // creates a task object and initializes some data for this example
        $quote = new Quote();


        $form = $this->createForm(QuoteType::class, $quote);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $quote = $form->getData();
            $quoteRepository->add($quote, true);

            return $this->redirectToRoute('index');
        }

        return $this->renderForm('quote/new.html.twig', [
            'form' => $form,
        ]);
    }

}