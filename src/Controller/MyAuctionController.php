<?php

namespace App\Controller;
use App\Entity\Auction;
use App\Form\AuctionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MyAuctionController extends AbstractController
{
    /**
     * @Route("/my/", name="my_auction_index")
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
     * @Route("/my/auction/add", name="my_auction_add")
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

            if($auction->getStartingPrice() >= $auction->getPrice()){
                $form->get("startingPrice")->addError(new FormError("Cena wywoławcza nie może być mniejsza niż cena głowna"));
            }

            if($form->isValid()){
                $auction
                ->setStatus(Auction::STATUS_ACTIVE)
                ->setOwner($this->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($auction);
                $entityManager->flush();

                $this->addFlash("success", "Aukcja została dodana");

                return $this->redirectToRoute("my_auction_details", ["id" => $auction->getId()]);
            }

            $this->addFlash("error", "Aukcja nie została dodana");
            
        }
        return $this->render('my_auction/add.html.twig', ["form" => $form->createView()]);
    }

    /**
     * Details Action, shows details of certain auction
     * 
     * @Route("/my/auction/details/{id}", name="my_auction_details")
     * 
     * @param Auction $auction
     * 
     * @return Response 
     */
    public function detailsAction(Auction $auction)
    {
        if ($auction->getStatus() === Auction::STATUS_FINISHED)
            return $this->render('my_auction/finished.html.twig', ["auction" => $auction]);

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("my_auction_delete", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_DELETE)
            ->add("submit", SubmitType::class, ["label" => "Usuń"])
            ->getForm();

        $finishForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("my_auction_finish", ["id" => $auction->getId()]))
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

    /**
     * @Route("/my/action/edit/{id}", name="my_auction_edit")
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

            if($auction->getStartingPrice() >= $auction->getPrice()){
                $form->get("startingPrice")->addError(new FormError("Cena wywoławcza nie może być mniejsza niż cena głowna"));
            }

            if($form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($auction);
                $entityManager->flush();
    
                $this->addFlash("success", "Aukcja została edytowana");
    
                return $this->redirectToRoute("my_auction_details", ["id" => $auction->getId()]);
            } 

            $this->addFlash("error", "Aukcja nie została edytowana");
            
        }

        return $this->render("my_auction/edit.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("my/auction/delete/{id}", name="my_auction_delete", methods={"DELETE"})
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

        return $this->redirectToRoute("my_auction_index");
    }

    /**
     * @Route("my/auction/finish/{id}", name="my_auction_finish", methods={"POST"})
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

        return $this->redirectToRoute("my_auction_details", ["id" => $auction->getId()]);
    }
}
