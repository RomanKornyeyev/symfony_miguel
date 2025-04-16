<?php

namespace App\Controller;

use App\Entity\Nota;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class NotasController extends AbstractController
{
    #[Route('/notas', name: 'app_nota')]
    public function index(EntityManagerInterface $em): Response
    {
        $notas = $em->getRepository(Nota::class)->findAll();

        return $this->render('notas/index.html.twig', [
            'notas' => $notas,
        ]);
    }
}
