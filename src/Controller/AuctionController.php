<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Form\AuctionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class AuctionController extends AbstractController
{
    /**
     * Index auctions, Shows all auctions
     * 
     * @Route("/auctions", name="auction_index")
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
     * @Route("/auctions/details/{id}", name="auction_details")
     * 
     * @param Auction $auction
     * 
     * @return Response 
     */
    public function detailsAction(Auction $auction)
    {
        return $this->render('auction/details.html.twig', ['auction' => $auction]);
    }

    /**
     * Form to add auction
     * 
     * @Route("/auctions/add", name="auction_add")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function addAction(Request $request)
    {
        $auction = new Auction();

        $form = $this->createForm(AuctionType::class, $auction);

        if ($request->isMethod('post')){
            $form->handleRequest($request);

            $auction
                ->setStatus(Auction::STATUS_ACTIVE);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($auction);
            $entityManager->flush();

            return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
        }
        return $this->render('auction/add.html.twig', ["form" => $form->createView()]);
    }

    /**
     * @Route("/auctions/edit/{id}", name="auction_edit")
     * 
     * @param Request $request
     * @param Auction $auction 
     * 
     * @return Response
     */
    public function editAction(Request $request, Auction $auction)
    {
        $form = $this->createForm(AuctionType::class, $auction);

        if ($request->isMethod("post")) {
            $form->handleRequest($request);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($auction);
            $entityManager->flush();

            return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
        }

        return $this->render("auction/edit.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("auctions/delete/{id}", name="auction_delete")
     */
    public function deleteAction(Auction $auction)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($auction);
        $entityManager->flush();

        return $this->redirectToRoute("auction_index");
    }
}
