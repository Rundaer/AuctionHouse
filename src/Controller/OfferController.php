<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Offer;
use App\Form\BidType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class OfferController extends AbstractController
{
    /**
     * @Route("auctions/buy/{id}", name="offer_buy", methods={"POST"})
     * 
     * @param Auction $auction
     */
    public function buyAction(Auction $auction)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $offer = new Offer();
        $offer
            ->setAuction($auction)
            ->setType(Offer::TYPE_BUY)
            ->setPrice($auction->getPrice())
            ->setOwner($this->getUser());

        $auction
            ->setStatus(Auction::STATUS_FINISHED)
            ->setExpiresAt(new \DateTime());
            

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($auction);
        $entityManager->persist($offer);
        $entityManager->flush();

        $this->addFlash("success", "Kupiłeś przedmiot");

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }

    /**
     * @Route("auctions/bid/{id}", name="offer_bid", methods={"POST"})
     */
    public function bidAction(Request $request, Auction $auction)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $offer = new Offer();
        $bidForm = $this->createForm(BidType::class, $offer);

        $bidForm->handleRequest($request);

        if($bidForm->isValid()){
            $entityManager = $this->getDoctrine()->getManager();

            $lastOffer = $entityManager
                ->getRepository(Offer::class)
                ->findOneBy(["auction" => $auction], ["createdAt" => "DESC"]);

            if(isset($lastOffer)){
                if($offer->getPrice() <= $lastOffer->getPrice()){
                    $this->addFlash("error", "Oferta nie może być niższa niż {$lastOffer->getPrice()} zł");
                    return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]); 
                }
            }

            $offer
                ->setType(Offer::TYPE_BID)
                ->setAuction($auction)
                ->setOwner($this->getUser());;

            $entityManager->persist($offer);
            $entityManager->flush();
    
            $this->addFlash("success", "Zalicytowałeś aukcje");
        } else {
            $this->addFlash("error", "Nieudana licytacja");
        }
        
        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }
}
