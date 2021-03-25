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
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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

        // $query = $this->em->createQuery(
        //     'SELECT cry.id, cry.name
        //     FROM App\Entity\Crypto cry
        //     ORDER BY cry.id'
        // );
        // $cryptos = $query->getResult();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {// && $apiTracker->isCryptoExist($form['cryptoName']->getData())//->getName()

            $contract = $form->getData();
            // $cryptoName = $form['crypto']->getData();

            // if(in_array($cryptoName, array_column($cryptos, 'name')))
            // {
            //     $contract->setCrypto(1);
            //     dd($contract);
            // }
            // else
            // {

            // }
            $this->em->persist($contract);
            $this->em->flush();
            return $this->redirectToRoute('app_contract_home');
        }

        return $this->render('contract/formAddContract.html.twig', [
            // 'cryptos' => $cryptos, 
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/contract/fluctuationGraphic", name="app_contract_fluctuationGraphic", methods={"GET"}) 
     */
    public function fluctuationGraphic(ChartBuilderInterface $chartBuilder): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'borderColor' => '#228551',
                    'data' => [0, 5, -10, 20, -15, 10, 8],
                    'borderCapStyle' => 'round',
                    'borderWidth' => 7,
                    'lineTension' => 0.8,
                    'fill' => false,
                ],
            ],
        ]);
        
        $chart->setOptions([
            'responsive' => true,
            'legend' => ['display' => false],
            'scales' => [
                'xAxes' => [ 'gridLines'=>  ['display'=> true,'drawBorder'=> true, 'drawOnChartArea'=> false] ],
                'yAxes'=>  [ 'gridLines'=>  ['display'=> true,'drawBorder'=> true, 'drawOnChartArea'=> false] ],
            ]
        ]);

        return $this->render('contract/fluctuationGraphic.html.twig', [
            'chart' => $chart,
        ]);
    }

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
