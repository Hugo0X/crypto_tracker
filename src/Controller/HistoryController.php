<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\History;
use App\Repository\HistoryRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;

/**
* @Route("/history")
*/
class HistoryController extends AbstractController
{
    /**
     * @Route("/", name="app_history_graph", methods={"GET"})
     */
    public function getGraph(HistoryRepository $historyRepository): Response
    {
        $history = $historyRepository->findBy([], ['id' => 'ASC']);

        $data = [];

        foreach ($history as $aHistory)
        {
            array_push($data, [$aHistory->getId(), $aHistory->getBenefit()]);
        }

        array_unshift($data, ['day', 'benefit'], [0,0]);

        $line = new LineChart();
        $line->getData()->setArrayToDataTable(
            $data
        );
   
        // $line->getOptions()->setWidth(500);
        $line->getOptions()->setHeight(300);

        $line->getOptions()->setColors(['#228551']);
        $line->getOptions()->setCurveType('function');
        $line->getOptions()->setLineWidth(4);
        $line->getOptions()->getLegend()->setPosition('none');
        $line->getOptions()->getBackgroundColor()->setFill('#100f0f');
        $line->getOptions()->setDataOpacity(0.1);
        $line->getOptions()->getTooltip()->setTrigger('none');

        $line->getOptions()->getHAxis()->getGridlines()->setColor('transparent');
        $line->getOptions()->getHAxis()->setTextPosition('none');
        $line->getOptions()->getHAxis()->setBaseLineColor('white');
        $line->getOptions()->getHAxis()->setTitle('date');
        $line->getOptions()->getHAxis()->getTitleTextStyle()->setColor('white');

        $line->getOptions()->getVAxis()->getGridlines()->setColor('transparent');
        $line->getOptions()->getVAxis()->setTextPosition('none');
        $line->getOptions()->getVAxis()->setBaseLineColor('white');
        $line->getOptions()->getVAxis()->setTitle('€');
        $line->getOptions()->getVAxis()->getTitleTextStyle()->setColor('white');



        // $line->getOptions()->setTrendlines([0 => ['opacity'=> 0.1, 'color'=> '#228551']]);



        // dd($line);

        return $this->render('history/graph.html.twig', ['chart' => $line]);
    }
}
