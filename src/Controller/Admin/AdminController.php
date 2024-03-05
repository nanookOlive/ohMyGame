<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin', methods: ['GET'])]
    public function index()
    {
        return $this->render('admin/board.html.twig', []);
    }
}
