<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompanyController extends AbstractController
{
    private CompanyRepository $companyRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, CompanyRepository $companyRepository)
    {
        $this->entityManager = $entityManager;
        $this->companyRepository = $companyRepository;
    }
    #[Route('/company', name: 'app_company')]
    public function index(): Response
    {
        $repository = $this->entityManager->getRepository(Company::class);

        $companies = $repository->findAll();

        return $this->render('company/index.html.twig', [
            'controller_name' => 'CompanyController',
            'companies' => $companies,
        ]);
    }

    #[Route('/company/edit', name: 'app_company_edit')]
    public function edit(Request $request): Response
    {
        $repository = $this->entityManager->getRepository(Company::class);

        $companies = $repository->find(1);

        if (!$companies) {
            throw $this->createNotFoundException('Aucune entreprise trouvée avec l\'ID : 1');
        }

        $form = $this->createForm(CompanyType::class, $companies);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'entreprise a été modifiée avec succès !');

            return $this->redirectToRoute('app_company');
        }

        return $this->render('company/edit.html.twig', [
            'controller_name' => 'CompanyController',
            'companies' => $companies,
            'form' => $form->createView(),
        ]);
    }
}
