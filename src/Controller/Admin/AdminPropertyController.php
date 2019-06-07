<?php


namespace App\Controller\Admin;


use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponseAlias;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * AdminPropertyController constructor.
     * @param PropertyRepository $repository
     * @param ObjectManager $em
     */
    public function __construct(PropertyRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.property.index")
     * @return Response
     */
    public function index()
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/index.html.twig', compact('properties'));
    }

    /**
     * @Route("/admin/create", name="admin.property.new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request)
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($property);
            $this->em->flush();
            $this->addFlash('success', 'Créer avec succes');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/{id}", name="admin.property.edit", methods="GET|POST")
     * @param Property $property
     * @param Request $request
     * @return Response
     */
    public function edit(Property $property, Request $request)
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Modifier avec succes');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{id}", name="admin.property.delete", methods="DELETE")
     * @param Property $property
     * @return RedirectResponseAlias
     */
    public function delete(Property $property, Request $request)
    {
        dump('suppression');
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))) {
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('success', 'Supprimer avec succes');
        }
        return $this->redirectToRoute('admin.property.index');
    }
}