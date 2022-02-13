<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\EditProductFormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $productList = $em->getRepository(Product::class)->findAll();
        dd($productList);

        return $this->render('main/default/index.html.twig');
    }


    /**
     * @Route("/edit-product/{id}", methods="GET|POST", name="product_edit", requirements={"id"="\d+"})
     * @Route("/add-product", methods="GET|POST", name="product_add")
     */
    public function editProduct(Request $request, int $id = null): Response
    {
        $em = $this->getDoctrine()->getManager();

        if ($id) {
            $product = $em->getRepository(Product::class)->find($id);
        } else {
            $product = new Product();
        }
        $form = $this->createForm(EditProductFormType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_edit', ["id" => $product->getId()]);
        }

        return $this->render('main/default/edit_product.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
