<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ApiTrackerController;
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

        return $this->render('contract/index.html.twig', [ 'contract' => $contract ]);
    }

    /**
     * @Route("/addContract", name="app_contract_add", methods={"GET","POST"})
     */
    public function formAddContract(Request $request, ApiTrackerController $apiTracker): Response
    {    
        $contract = new Contract;
        $form = $this->createForm(ContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $apiTracker->isCryptoExist($form['crypto']->getData()->getName())) { 

            $contract = $form->getData();
            // $contract->setAcronym($apiTracker->getAcronym($contract->getName()));

            $this->em->persist($contract);
            $this->em->flush();

            return $this->redirectToRoute('app_contract_home');
        }

        return $this->render('contract/formAddContract.html.twig', [
            'form' => $form->createView(), 
        ]);
    }

    /**
     * @Route("/delete/{id<[0-9]+>}", name="app_contract_delete", methods={"GET","DELETE"})
     */
    public function delete(Request $request, Contract $contract): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contract->getId(), $request->request->get('_token')) && is_numeric($request->request->get('quantityDelete'))) {
            
            $quantityDelete = $request->request->get('quantityDelete');

            if($quantityDelete > $contract->getQuantity())
            {
                $this->em->remove($contract);
            }
            else
            {
                $contract->setQuantity($contract->getQuantity() - $quantityDelete);
                $this->em->persist($contract);
            }
            $this->em->flush();

            return $this->redirectToRoute('app_contract_home');
        }

        return $this->render('contract/delete.html.twig', [ 'contract' => $contract ]);
    }

}
