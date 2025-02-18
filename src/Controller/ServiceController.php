<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\MeasurementRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/service')]
class ServiceController extends AbstractController
{
    #[Route('/', name: 'app_service_index', methods: ['GET'])]
    public function index(
        ServiceRepository $serviceRepository,
        MeasurementRepository $measureRepository
    ): Response {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
            'measurements' => $measureRepository->findAll(),
        ]);
    }

    #[Route('/ajouter-service', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ServiceRepository $serviceRepository): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service, ['validation_groups' => ['add']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->add($service, true);

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/new.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/{id}/modifier-service', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        $form = $this->createForm(ServiceType::class, $service, ['validation_groups' => ['default']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->add($service, true);
            $this->addFlash('success', 'Votre service a bien été modifié');

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
            $serviceRepository->remove($service, true);
        }

        $this->addFlash('danger', 'Service supprimé');
        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }
}
