<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Product;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Session\Session;


class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {



        $products = $this->get('doctrine_mongodb')
            ->getManager()
            ->createQueryBuilder('AppBundle:Product')

            ->limit(100)
            ->skip(0)

            ->getQuery()
            ->execute();

        if($request->get('start')){
            $products = $this->get('doctrine_mongodb')
                ->getManager()
                ->createQueryBuilder('AppBundle:Product')

                ->limit(100)
                ->skip($request->get('start'))

                ->getQuery()
                ->execute();
        }





        return $this->render('products/index.html.twig',array(
            'products'=>$products,
        ));




    }

    public function createAction()
    {


        return $this->render('products/create.html.twig');
    }


    public function createSubmitAction(Request $request)
    {
        if($request->getMethod() == "POST"){
            $product = new Product();
            $product->setProductId($request->get('productId'));
            $product->setProductName($request->get('productName'));
            $product->setRetailPrice($request->get('retailPrice'));
            $product->setDescription($request->get('description'));

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($product);
            $dm->flush();

            $this->addFlash("success", "Le produit a été ajouté avec succès.");

            return $this->redirectToRoute('ydrive_product_index');
        }
        else{
            return new Response('Erreur, la création à échouée.');
        }

        
    }



    public function showAction($id)
    {
        $product = $this->get('doctrine_mongodb')
            ->getRepository('AppBundle:Product')
            ->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        return $this->render('products/show.html.twig',array(
            'product'=>$product,
        ));
        // do something, like pass the $product object into a template
    }


    public function updateAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = $dm->getRepository('AppBundle:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }



        return $this->render('products/update.html.twig',array(
            'product'=>$product,
        ));
    }

    public function updateSubmitAction(Request $request, $id )
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = $dm->getRepository('AppBundle:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $product->setProductId($request->get('productId'));
        $product->setProductName($request->get('productName'));
        $product->setPrice($request->get('retailPrice'));
        $product->setDescription($request->get('description'));
        $product->setStock($request->get('stock'));
        $dm->flush();

        return $this->redirectToRoute('ydrive_product_index');
    }

    public function deleteAction( $id )
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = $dm->getRepository('AppBundle:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $dm->remove($product);
        $dm->flush();

        return $this->redirectToRoute('ydrive_product_index');
    }

    public function addCartAction( $id )
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = $dm->getRepository('AppBundle:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $prdId = $product->getId();
        $session = $this->container->get('session');
        $session->set('cart', $session->get('cart'). '/'.$prdId);

        $this->addFlash("success", "Le produit a bien été ajouté au panier.");




        return $this->redirectToRoute('ydrive_product_index');
    }

    public function cartIndexAction( )
    {
        $session = $this->container->get('session');

        $a = $session->get('cart');

        $a = explode('/', $a);

        $products = array();
        $dm = $this->get('doctrine_mongodb')->getManager();

        foreach ($a as $id){
            $product = $dm->getRepository('AppBundle:Product')->find($id);
            array_push($products, $product)   ;
        }







        return $this->render('cart/index.html.twig',array(
            'products'=>$products,
        ));
    }




    public function removeCartAction( $id )
    {
        $session = $this->container->get('session');

        $a = $session->get('cart');

        $a = explode('/', $a);

        //$a = str_replace($id, "", $a);
        $from = '/'.preg_quote($id, '/').'/';
        $to ='';
        $a =  preg_replace($from, $to, $a, 1);

        $session->set('cart', implode("/", $a));



        $this->addFlash("success", "Le produit a bien été retiré du panier.");

        return $this->redirectToRoute('ydrive_cart_index');
    }



}
