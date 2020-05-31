<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Form\AuctionType;
use App\Form\BidType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
        $auctions = $entityManager->getRepository(Auction::class)->findBy(["status" => Auction::STATUS_ACTIVE]);

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
        if ($auction->getStatus() === Auction::STATUS_FINISHED)
            return $this->render('my_auction/finished.html.twig', ["auction" => $auction]);

        $buyForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("offer_buy", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_POST)
            ->add("submit", SubmitType::class, ["label" => "Kup"])
            ->getForm();

        $bidForm = $this->createForm(
            BidType::class, 
            null, 
            ["action" => $this->generateUrl("offer_bid", ["id" => $auction->getId()])]
        );

        return $this->render(
            'auction/details.html.twig',
            [
                'auction' => $auction, 
                "buyForm" => $buyForm->createView(),
                "bidForm" => $bidForm->createView(),
            ]
        );
    }

    
}
