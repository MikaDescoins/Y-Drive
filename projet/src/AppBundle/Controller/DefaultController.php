<?php

namespace AppBundle\Controller;

use AppBundle\Document\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Product;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\DateTime;


class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
       // $user = $this->get('security.token_storage')->getToken()->getUser();



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

    public function createOrderAction( )
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $session = $this->container->get('session');
        $a = $session->get('cart');


        if($a != ''){
        // ------------- CREATION DE L'ORDER ----------------
        $order = new Order();
        $order->setUser($user);




        $a = explode('/', $a);

        $products = array();


        $total = 0;

        foreach ($a as $id){
            $product = $dm->getRepository('AppBundle:Product')->find($id);
            array_push($products, $product)   ;
            if($product){
                $order->addProduct($product);
                $total += $product->getPrice();
                $product->setStock($product->getStock()-1);
            }

        }

        $order->setTotal($total);
        $date = new \DateTime();
        $order->setDateOrder($date);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($order);
        $dm->flush();


        // ------------- TRAITEMENT POST ORDER ----------------

        $session->set('cart', '');



        return $this->render('order/success.html.twig',array(
            'order'=>$order,
        ));

        }
        else{
            return $this->render('order/error.html.twig'
            );
        }

    }


    public function indexOrderAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $orders = $dm->getRepository('AppBundle:Order')->findBy(array('user.id'=>$user->getId()));





        return $this->render('order/index.html.twig',array(
            'orders'=>$orders
        ));



    }

    public function indexApiAction(Request $request)
    {
        if($request){
            $json = file_get_contents('http://webhose.io/productFilter?token=d195e189-db6b-4b2c-b1a5-45c76eec2e1d&format=json&q='.$request->get("product"));
            $obj = json_decode($json);





            return $this->render('api/products.html.twig', array('products'=>$obj));
        }


        return $this->render('api/products.html.twig');

        $this->addFlash("error", "Erreur, votre requête n'a pas pu aboutir..");




        return $this->redirectToRoute('ydrive_api_search');


    }

    public function searchApiAction()
    {


        return $this->render('api/search.html.twig');




    }


}
