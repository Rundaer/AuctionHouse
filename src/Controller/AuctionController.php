<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AuctionController extends AbstractController
{
    /**
     * Index auctions, Shows all auctions
     * 
     * @Route("/", name="auction_index")
     */
    public function indexAction()
    {
        $auctions = [];

        for ($i = 1; $i < 5; $i++){
            array_push($auctions, [
                'id'            => $i,
                'title'         => 'Aukcja '.$i,
                'description'   => 'Opis aukcji '.$i,
                'price'         => 5 * $i,
            ]);
        }

        return $this->render('auction/index.html.twig', ['auctions' => $auctions]);
    }

    /**
     * Details Action, shows details of certain auction
     * 
     * @Route("/{id}", name="auction_details")
     * 
     * @param $id
     */
    public function detailsAction($id)
    {
        return $this->render('auction/details.html.twig');
    }
}
