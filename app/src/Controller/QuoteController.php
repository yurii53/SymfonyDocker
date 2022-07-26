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

        $defaultContext = [
            AbstractNormalizer::GROUPS             => ['user'],
            AbstractNormalizer::IGNORED_ATTRIBUTES => [
                '__initializer', 'isInitialized',
                'cloner'
            ]
        ];
        $encoders = [new JsonEncoder()];
        $normalizer = new ObjectNormalizer(
            null, null, null,
            null, null, null,
            $defaultContext
        );
        $normalizers = [$normalizer];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/', name: 'index')]
    public function index(
        QuoteRepository $quoteRepository,
        DeathNoteRepository $noteRepository
    ): Response
    {

        return $this->render(
            'index.html.twig',
            [
                'quotes'  => $quoteRepository->findAll(),
                'persons' => $noteRepository->findAll(),
            ]
        );
    }

    #[Route('/api/quote/', name: 'index1')]
    public function index1(
        SerializerInterface $serializer,
        QuoteRepository $quoteRepository,
        DeathNoteRepository $noteRepository
    ): Response
    {
        $response = $serializer->serialize($noteRepository->findAll(), JsonEncoder::FORMAT, [
            AbstractNormalizer::GROUPS => ['quotes']
        ]);

        /*$quoteRepository->findAll() */

        return new Response($response, Response::HTTP_OK, ['Content-type' => 'application/json']);
    }

    function Year($b){
        return $b->getYear();
    }
    #[Route('/api/quote1/', name: 'index2')]
    public function index2(
        SerializerInterface $serializer,
        QuoteRepository $quoteRepository,
        DeathNoteRepository $noteRepository
    ): Response
    {

        $response1 = $serializer->serialize($quoteRepository->findAll(), JsonEncoder::FORMAT, [
            AbstractNormalizer::GROUPS => ['quotes']
        ]);


        /*$quoteRepository->findAll() */

        return new Response(
            $response1, Response::HTTP_OK
        );
    }

}