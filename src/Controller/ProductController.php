<?php

namespace App\Controller;

use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority="-1")
     * @param $slug
     * @param CategoryRepository $repository
     * @return Response
     */
    public function category($slug, CategoryRepository $repository)
    {
        $category = $repository->findOneBy([
            'slug'=> $slug
        ]);
        if (!$category)
        {
            throw $this->createNotFoundException('y a pas de catégory putain !!');
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);

    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show", priority="-1")
     * @param $slug
     * @param ProductRepository $productRepository
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     */
    public function show($slug, ProductRepository $productRepository, EventDispatcherInterface $dispatcher)
    {
        $product= $productRepository->findOneBy([
            'slug'=> $slug
        ]);

        if (!$product)
        {
            throw $this->createNotFoundException('y a pas de produit ici putain !!');
        }
        //event
        $dispatcher->dispatch(new ProductViewEvent($product), 'product.view');
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="edit")
     * @param $id
     * @param ProductRepository $productRepository
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    // exemple acces de droit dans les annotations   * @IsGranted("ROLE_ADMIN", message="vous n'avez pas le droit d'acceder à cette ressource")
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em):Response
    {
        $product = $productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product);
/*        $form->setData($product);*/ //soit ca soit passer $product dans create form
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
/*            $product = $form->getData();*/ //pas de cette ligne car $product est deja dans le create form (hydratation)
           //pas besoin de 'persist' pour une modif car l'objet est deja dans la base
            $em->flush();
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }
        $formView = $form->createView();

        return $this->render('/product/edit.html.twig', [
            'product' => $product,
            'form' => $formView,
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class );
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ){
            $product = $form->getData();//ou instancier l'objet au debut de la fonction et le mettre dans le createform
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();
        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
        }
        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'form' => $formView,
        ]);
    }
}
