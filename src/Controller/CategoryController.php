<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use ContainerHjvKMK3\getUserRepositoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = new Category();
         $form = $this->createForm(CategoryType::class, $category);
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid())
         {
             $category->setSlug(strtolower($slugger->slug($category->getName())));
             $em->persist($category);
             $em->flush();
             return $this->redirectToRoute('home');
         }
         $formView = $form->createView();
        return $this->render('category/create.html.twig', [
            'formView' => $formView,
        ]);
    }

    /**
     * @Route("admin/category/{id}/edit", name="category_edit")
     * @param $id
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $category = $categoryRepository->findOneBy([
            'id'=> $id
        ]);
        if (!$category)
        {
            throw new NotFoundHttpException('cette catégorie n\'existe pas');
        }
/*        $this->denyAccessUnlessGranted('CAN_EDIT', $category, "vous n\'ete pas le propirétaire de cette catégory");*/
        /*$user = $this->getUser();
        if (!$user)
        {
            return $this->redirectToRoute('security_login');
        }
        if($user !== $category->getOwner())
        {
            throw new AccessDeniedException('vous n\'etes pas celui qui a crée la category');
        }*/
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->flush();
            return $this->redirectToRoute('home');
        }
        $formView = $form->createView();

        return $this->render('/category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView,
        ]);
    }
}
