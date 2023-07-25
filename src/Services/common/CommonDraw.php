<?php

namespace App\Services;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Services\Common;

require_once 'constantesUtiles.php';

class CommonDraw {

    private $common;

    public function __construct(){
        $this->common = new Common();
    }
    
    /**
     * méthode de tracer de nos graphes
     */
    public function draw(ChartBuilderInterface $chartBuilder, $type, $data, $label = '', $isAnotherChart = false, $data2 = [], $labelAnotherChart = '')
    {
        $borne1 = empty($data) ? 0 : max($data);
        $borne2 = empty($data2) ? 0 : max($data2);
        $chart = $chartBuilder->createChart($type);
        $datasets = [
            [
                'label' => $label,
                'backgroundColor' => '#FF9C33',
                'borderColor' => '#FF9C33',
                'borderWidth' => 1,
                'data' => array_values($data),
                'tension' => 0.3,
                'pointRadius' => 5,
            ],
        ];
        $scales = [
            'x' => [
                'grid' => [
                    'display' => false,
                ],
            ],
            'y' => [
                'id' => 'suivi_distance',
                'position' => 'left',
                'suggestedMin' => 0,
                'suggestedMax' => $borne1,
            ],
        ];
        if ($isAnotherChart && $data2 !== null) {
            $datasets[] = [
                'label' => $labelAnotherChart,
                'yAxisId' => 'objectif_distance',
                'backgroundColor' => '#615FD7',
                'borderColor' => '#615FD7',
                'borderWidth' => 1,
                'data' => array_values($data2),
                'tension' => 0.3,
                'pointRadius' => 5,
            ];
            $scales['y2'] = [
                'id' => 'objectif_distance',
                'position' => 'right',
                'suggestedMin' => 0,
                'suggestedMax' => $borne2,
            ];
        }
        $chart->setData([
            'labels' => array_keys($data),
            'datasets' => $datasets,
        ]);
        $chart->setOptions([
            'scales' => $scales,
        ]);
        return $chart;
    }

    /**
     * tracer un graphe mixte
     */
    public function drawMixed(ChartBuilderInterface $chartBuilder, $dataBars, $dataLine, $labelBars = '', $secondLabel = '')
    {
        $chart = $chartBuilder->createChart(BAR_CHART);

        $datasets = [
            [
                'type' => BAR_CHART,
                'label' => $labelBars,
                'backgroundColor' => 'rgba(255, 156, 51, 0.8)',
                'borderColor' => 'rgba(255, 156, 51, 1)',
                'borderWidth' => 1,
                'data' => array_values($dataBars),
            ],
            [
                'type' => LINEAR_CHART,
                'label' => $secondLabel,
                'backgroundColor' => 'rgba(97, 95, 215, 0.7)',
                'borderColor' => 'rgba(97, 95, 215, 1)',
                'borderWidth' => 1,
                'data' => array_values($dataLine),
                'fill' => true,
                'tension' => 0.4,
                'pointRadius' => 6,
            ],
        ];

        $scales = [
            'x' => [
                'grid' => [
                    'display' => false,
                ],
            ],
            'y' => [
                'id' => 'suivi_distance',
                'position' => 'left',
                'suggestedMin' => 0,
                'suggestedMax' => max(array_merge($dataBars, $dataLine)),
            ],
        ];

        $chart->setData([
            'labels' => array_keys($dataBars),
            'datasets' => $datasets,
        ]);
        $chart->setOptions([
            'scales' => $scales,
        ]);

        return $chart;
    }


    /**
     * méthode permettant d'obtenir des tracers linéaires selon les périodes sélectionnées
     */
    public function drawLineWhenIsSelected(ChartBuilderInterface $chartBuilder, $periode, $userHistoriques, $userObjectifs, $form, $domaine, $activite, $label, $secondLabel, $data){
        $type = LINEAR_CHART;
        switch ($periode) {
            case '7':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->draw($chartBuilder, $type, $pasEffectues, $label, true, $objectifPas, $secondLabel),
                ];
                break;
            case '14':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->draw($chartBuilder, $type, $pasEffectues, $label, true, $objectifPas, $secondLabel),
                ];
                break;
            case '30':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->draw($chartBuilder, $type, $pasEffectues, $label, true, $objectifPas, $secondLabel),
                ];
                break;
            case '60':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->draw($chartBuilder, $type, $pasEffectues, $label, true, $objectifPas, $secondLabel),
                ];
                break;
        }
    }

    /**
     * méthode permettant d'obtenir des tracers en bar selon les périodes sélectionnées
     */
    public function drawBarWhenIsSelected(ChartBuilderInterface $chartBuilder, $periode, $userHistoriques, $userObjectifs, $form, $domaine, $activite, $label, $secondLabel, $data)
    {
        $type = BAR_CHART;
        switch ($periode) {
            case '7':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->draw($chartBuilder, $type, $pasEffectues, $label, true, $objectifPas, $secondLabel),
                ];
                break;
            case '14':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->draw($chartBuilder, $type, $pasEffectues, $label, true, $objectifPas, $secondLabel),
                ];
                break;
            case '30':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->draw($chartBuilder, $type, $pasEffectues, $label, true, $objectifPas, $secondLabel),
                ];
                break;
            case '60':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->draw($chartBuilder, $type, $pasEffectues, $label, true, $objectifPas, $secondLabel),
                ];
                break;
        }
    }

    /**
     * méthode permettant d'obtenir des tracers mixtes selon les périodes sélectionnées
     */
    public function drawMixedWhenIsSelected(ChartBuilderInterface $chartBuilder, $periode, $userHistoriques, $userObjectifs, $form, $domaine, $activite, $label, $secondLabel, $data)
    {
        $type = LINEAR_CHART;
        switch ($periode) {
            case '7':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->drawMixed($chartBuilder,  $pasEffectues, $objectifPas, $label,  $secondLabel),
                ];
                break;
            case '14':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->drawMixed($chartBuilder,  $pasEffectues, $objectifPas, $label,  $secondLabel),
                ];
                break;
            case '30':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->drawMixed($chartBuilder,  $pasEffectues, $objectifPas, $label,  $secondLabel),
                ];
                break;
            case '60':
                $pasEffectues = $this->common->getDataSurDerniersJours($userHistoriques, $domaine, $activite, intval($periode, 10));
                $objectifPas = $this->common->getDataSurDerniersJours($userObjectifs, $domaine, $activite, intval($periode, 10));
                return [
                    'datas' => $data,
                    'form' => $form->createView(),
                    'chart_pas' => $this->draw($chartBuilder, $type, $pasEffectues, $label),
                    'chart_pas_objectif' => $this->drawMixed($chartBuilder,  $pasEffectues, $objectifPas, $label,  $secondLabel),
                ];
                break;
        }
    }



}