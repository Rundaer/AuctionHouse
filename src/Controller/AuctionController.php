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
                "deleteForm" => $deleteForm->createView(),
                "finishForm" => $finishForm->createView(),
                "buyForm" => $buyForm->createView(),
                "bidForm" => $bidForm->createView(),
            ]
        );
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
        $this->denyAccessUnlessGranted("ROLE_USER");

        $auction = new Auction(); 

        $form = $this->createForm(AuctionType::class, $auction);

        if ($request->isMethod('post')){
            $form->handleRequest($request);

            if($form->isValid()){
                $auction
                ->setStatus(Auction::STATUS_ACTIVE)
                ->setOwner($this->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($auction);
                $entityManager->flush();

                $this->addFlash("success", "Aukcja została dodana");

                return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
            }

            $this->addFlash("error", "Aukcja nie została dodana");
            
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
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $auction->getOwner()){
            throw new AccessDeniedException(); 
        } 

        $form = $this->createForm(AuctionType::class, $auction);

        if ($request->isMethod("post")) {
            $form->handleRequest($request);

            if($form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($auction);
                $entityManager->flush();
    
                $this->addFlash("success", "Aukcja została edytowana");
    
                return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
            } 

            $this->addFlash("error", "Aukcja nie została edytowana");
            
        }

        return $this->render("auction/edit.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("auctions/delete/{id}", name="auction_delete", methods={"DELETE"})
     * 
     * @param Auction $auction 
     * 
     * @return Response
     */
    public function deleteAction(Auction $auction)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $auction->getOwner()){
            throw new AccessDeniedException(); 
        } 

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($auction);
        $entityManager->flush();

        $this->addFlash("success", "Aukcja została usunięta");

        return $this->redirectToRoute("auction_index");
    }

    /**
     * @Route("auctions/finish/{id}", name="auction_finish", methods={"POST"})
     * 
     * @param Auction $auction 
     * 
     * @return Response
     */
    public function finishAction(Auction $auction)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $auction->getOwner()){
            throw new AccessDeniedException(); 
        } 

        $auction
            ->setExpiresAt(new \DateTime())
            ->setStatus(Auction::STATUS_FINISHED);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($auction);
        $entityManager->flush();

        $this->addFlash("success", "Aukcja została zakończona");

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }
}
