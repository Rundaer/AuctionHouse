<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Offer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends AbstractController
{
    /**
     * @Route("auctions/buy/{id}", name="offer_buy", methods={"POST"})
     * 
     * @param Auction $auction
     */
    public function buyAction(Auction $auction)
    {
        $offer = new Offer();
        $offer
            ->setAuction($auction)
            ->setType(Offer::TYPE_BUY)
            ->setPrice($auction->getPrice());

        $auction->setStatus(Auction::STATUS_FINISHED);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($auction);
        $entityManager->persist($offer);
        $entityManager->flush();

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }
}
