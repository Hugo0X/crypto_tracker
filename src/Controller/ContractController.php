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

// use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
// use Symfony\UX\Chartjs\Model\Chart;

// use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;

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
    public function formAddContract(Request $request, ApiTrackerController $apiTracker, ContractRepository $contractRepository): Response
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
            return $this->redirectToRoute('app_contract_home');
        }

        return $this->render('contract/formAddContract.html.twig', [ 
            'form' => $form->createView()
        ]);
    }


    // /**
    //  * @Route("/contract/fluctuationGraphic", name="app_contract_fluctuationGraphic", methods={"GET"}) 
    //  */
    // public function fluctuationGraphic(): Response
    // {
    //     $line = new LineChart();
    //     $line->getData()->setArrayToDataTable(
    //         [
    //             ['day', 'Sales'],
    //             [0, 0],
    //             ['1',  1000],
    //             ['2',  1170],
    //             ['3',  -660 ],
    //             ['4',  1030]
      
    //         ]);
   
    // $line->getOptions()->setColors(['#228551']);
    // $line->getOptions()->setCurveType('function');
    // $line->getOptions()->setLineWidth(6);
    // $line->getOptions()->getLegend()->setPosition('none');
    // $line->getOptions()->getBackgroundColor()->setFill('#100f0f');
    // $line->getOptions()->setDataOpacity(0.1);

    // $line->getOptions()->getHAxis()->getGridlines()->setColor('transparent');
    // $line->getOptions()->getHAxis()->setTextPosition('none');
    // $line->getOptions()->getHAxis()->setBaseLineColor('white');

    // $line->getOptions()->getVAxis()->getGridlines()->setColor('transparent');
    // $line->getOptions()->getVAxis()->setTextPosition('none');
    // $line->getOptions()->getVAxis()->setBaseLineColor('white');

    // // $line->getOptions()->setTrendlines([0 => ['opacity'=> 0.1, 'color'=> '#228551']]);



    // // dd($line);

    // return $this->render('contract/fluctuationGraphic.html.twig', ['chart' => $line]);
    // }

    /**
     * @Route("/delete/{id<[0-9]+>}", name="app_contract_delete", methods={"GET","DELETE"})
     */
    public function delete(Request $request, Contract $contract): Response
    {
        if ($this->isCsrfTokenValid('contract_deletion_'.$contract->getId(), $request->request->get('_token')) && is_numeric($request->request->get('quantityDelete')) && $request->request->get('quantityDelete') >= 0.000001) {
            
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
