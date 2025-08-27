<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Facture;
use App\Entity\Devis;
use App\Repository\FactureRepository;
use App\Repository\DevisRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;


final class FactureController extends AbstractController
{
    #[Route('/facture', name: 'app_facture')]
    public function facture(
        Request $request,
        FactureRepository $factureRepository
    ): Response {
        $page = $request->query->getInt('page', 1);
        $limit = 10; // Nombre de factures par page

        // On appelle la méthode de notre repository pour récupérer les factures de la page actuelle
        $factures = $factureRepository->findWithPagination($page, $limit);

        // On appelle la méthode de notre repository pour compter toutes les factures
        $totalFactures = $factureRepository->countAll();
        $totalPages = ceil($totalFactures / $limit);

        return $this->render('facture/index.html.twig', [
            'factures' => $factures,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/facture/{id}', name: 'facture_show')]
    public function showFacture(
        Request $request,
        Facture $facture,
       
        DevisRepository $devisRepository,
       
    ): Response
    {
        $devisId = $facture->getDevis()->getId();
        $devis = $devisRepository->find($devisId);
       

        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
            'devis' => $devis,
        ]);
    }
   
    #[Route('/facture/pdf/{id}', name: 'facture_pdf')]
    public function exportPdf(
        Devis $devis,
        EntityManagerInterface $entityManager,
        Environment $twig,
        int $id
    ): Facture {

        $facture = $entityManager-> getRepository(Facture::class)->find($id);

        // Générer le PDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', false);
        $dompdf = new Dompdf($options);

        $html = $twig->render('facture/pdf_template.html.twig', [
            'facture' => $facture,
            'devis' => $devis,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Désactive la barre de debug si elle est active
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        // Sauvegarde le fichier sur le serveur
        $pdfDir = $this->getParameter('kernel.project_dir') . '/public/uploads/factures';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }
        $pdfFilename = $facture->getNumero() . '.pdf';
        $pdfPath = $pdfDir . '/' . $pdfFilename;
        file_put_contents($pdfPath, $dompdf->output());


        // enregistrer le chemin dans la base de données
        $facture->setFacture('/uploads/factures/'.$pdfFilename);
        $entityManager->persist($facture);
        $entityManager->flush();

        return $facture;


 }

}
