<?php

namespace App\Controller;
use App\Entity\Auction;
use App\Form\BidType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class MyAuctionController extends AbstractController
{
    /**
     * @Route("/my", name="my_auction_index")
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();
        $auctions = $entityManager
            ->getRepository(Auction::class)
            ->findBy(["owner" => $this->getUser()]);

        return $this->render("my_auction/index.html.twig", ["auctions" => $auctions]);
    }

    /**
     * Details Action, shows details of certain auction
     * 
     * @Route("/my/details/{id}", name="my_auction_details")
     * 
     * @param Auction $auction
     * 
     * @return Response 
     */
    public function detailsAction(Auction $auction)
    {
        if ($auction->getStatus() === Auction::STATUS_FINISHED)
            return $this->render('auction/finished.html.twig', ["auction" => $auction]);

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("auction_delete", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_DELETE)
            ->add("submit", SubmitType::class, ["label" => "Usuń"])
            ->getForm();

        $finishForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("auction_finish", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_POST)
            ->add("submit", SubmitType::class, ["label" => "Zakończ"])
            ->getForm();


        return $this->render(
            'my_auction/details.html.twig',
            [
                'auction' => $auction, 
                "deleteForm" => $deleteForm->createView(),
                "finishForm" => $finishForm->createView()
            ]
        );
    }
}
