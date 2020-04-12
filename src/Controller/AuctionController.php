<?php

namespace App\Controller;

use App\Entity\Auction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AuctionController extends AbstractController
{
    /**
     * Index auctions, Shows all auctions
     * 
     * @Route("/", name="auction_index")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $auctions = $entityManager->getRepository(Auction::class)->findAll();

        return $this->render('auction/index.html.twig', ['auctions' => $auctions]);
    }

    /**
     * Details Action, shows details of certain auction
     * 
     * @Route("/auction/{id}", name="auction_details")
     * 
     * @param $id
     */
    public function detailsAction($id)
    {
        return $this->render('auction/details.html.twig');
    }

    /**
     * Form to add auction
     * 
     * @Route("/add", name="auction_add")
     * 
     * @return Response
     */
    public function addAction(Request $request)
    {
        $auction = new Auction();

        $form = $this->createFormBuilder($auction)
            ->add('title',          TextType::class)
            ->add('description',    TextareaType::class)
            ->add('price',          NumberType::class)
            ->add('submit',         SubmitType::class)
            ->getForm();

        if ($request->isMethod('post')){
            $form->handleRequest($request);

            $auction = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($auction);
            $entityManager->flush();

            return $this->redirectToRoute("auction_index");
        }
        return $this->render('auction/add.html.twig', ["form" => $form->createView()]);
    }
}
