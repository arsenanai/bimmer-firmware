<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SoftwareDownloadController extends AbstractController
{
    private const SHOP_URL = 'https://newshop.testshop1.bimmer-tech.net/';

    #[Route('/carplay/software-download', name: 'software_download')]
    public function index(): Response
    {
        return $this->render('software/download.html.twig', [
            'shop_url' => $this->getParameter('app.shop_url') ?: self::SHOP_URL,
        ]);
    }
}
