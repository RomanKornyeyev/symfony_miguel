<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $graphicsCards = [
            [
                'brand' => 'NVIDIA',
                'model' => 'RTX 4090',
                'memory' => '24GB'
            ],
            ['brand' => 'AMD', 'model' => 'Radeon RX 7900 XTX', 'memory' => '24GB'],
            ['brand' => 'NVIDIA', 'model' => 'RTX 4080', 'memory' => '16GB'],
            ['brand' => 'AMD', 'model' => 'Radeon RX 7800 XT', 'memory' => '16GB'],
            ['brand' => 'NVIDIA', 'model' => 'RTX 4070 Ti', 'memory' => '12GB'],
            ['brand' => 'AMD', 'model' => 'Radeon RX 7600', 'memory' => '8GB'],
            ['brand' => 'NVIDIA', 'model' => 'RTX 4060', 'memory' => '8GB'],
            ['brand' => 'Intel', 'model' => 'Arc A770', 'memory' => '16GB'],
        ];

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'graphics_cards' => $graphicsCards,
        ]);
    }
}
