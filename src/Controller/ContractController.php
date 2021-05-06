<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ApiTrackerController;
use App\Controller\Crypto;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contract;
use App\Repository\ContractRepository;
use App\Form\ContractType;
use Doctrine\ORM\EntityManagerInterface;


class ContractController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="app_contract_home", methods={"GET"})
     */
    public function index(ContractRepository $contractRepository, Request $request): Response
    {
        $contract = $contractRepository->findBy([], ['price' => 'DESC']);

        return $this->render('contract/index.html.twig', [ 'contracts' => $contract ]);
    }

    /**
     * @Route("/contract/new", name="app_contract_add", methods={"GET","POST"}) 
     */
    public function formAddContract(Request $request, ContractRepository $contractRepository): Response
    {   
        $contract = new Contract;
        $form = $this->createForm(ContractType::class, $contract);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {// && $apiTracker->isCryptoExist($form['cryptoName']->getData())//->getName()

            $contract = $form->getData();
            // $cryptoName = $form['crypto']->getData();
            // if(in_array($cryptoName, array_column($cryptos, 'name')))
            $this->em->persist($contract);
            $this->em->flush();

            $this->addFlash('success', 'La transaction a bien été ajoutée.');
            return $this->redirectToRoute('app_contract_home');
        }

        return $this->render('contract/formAddContract.html.twig', [ 
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id<[0-9]+>}", name="app_contract_delete", methods={"GET","DELETE"})
     */
    public function delete(Request $request, Contract $contract, ApiTrackerController $apiTracker): Response
    {
        if ($this->isCsrfTokenValid('contract_deletion_'.$contract->getId(), $request->request->get('_token')) && is_numeric($request->request->get('quantityDelete')) && $request->request->get('quantityDelete') >= 0.000001) {
            
            $quantityDelete = $request->request->get('quantityDelete');

            if($quantityDelete > $contract->getQuantity())
            {
                $this->em->remove($contract);
            }
            
            {
                $contract->setQuantity($contract->getQuantity() - $quantityDelete);
                $contract->setPrice($contract->getPrice() - $quantityDelete * $apiTracker->getCurrency($contract->getCrypto()->getName()));
                $this->em->persist($contract);
            }
            $this->em->flush();

            $this->addFlash('success', 'La suppression a bien été effectuée.');
            return $this->redirectToRoute('app_contract_home');
        }

        return $this->render('contract/delete.html.twig', [ 'contract' => $contract ]);
    }
}
