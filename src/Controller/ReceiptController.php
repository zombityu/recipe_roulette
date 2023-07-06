<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReceiptController extends AbstractController
{
    #[Route('/api/receipts', name: 'app_receipt')]
    public function getReceipts(): Response
    {
        $email = $this->getUser()->getUserIdentifier();


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ReceiptController.php',
        ]);
    }
}
