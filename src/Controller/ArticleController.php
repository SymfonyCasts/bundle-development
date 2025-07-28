<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(
        ArticleRepository $articles,
        TranslatorInterface $translator,
    ): Response {
        dump($translator->trans('Hello World!'));

        return $this->render('article/index.html.twig', [
            'articles' => $articles->findBy([], orderBy: ['publishedAt' => 'DESC']),
        ]);
    }

    #[Route('/news/{slug:article}', name: 'app_article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comments' => [
                'I ate a normal rock once. It did NOT taste like bacon!',
                'Woohoo! I\'m going on an all-asteroid diet!',
                'I like bacon too! Buy some from my site! bakinsomebacon.com',
            ],
        ]);
    }
}
