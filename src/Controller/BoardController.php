<?php

namespace App\Controller;

use App\Form\SelectType;
use App\Form\UserType;
use App\Repository\HistoriqueRepository;
use App\Repository\ObjectifRepository;
use App\Services\Common;
use App\Services\CommonDraw;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

class BoardController extends AbstractController
{

    private $common;
    private $commonDraw;

    public function __construct()
    {
        $this->common = new Common();
        $this->commonDraw = new CommonDraw();
    }


    #[Route('/board/pas', name: 'app_board')]
    public function index(
        HistoriqueRepository $historiqueRepository,
        ObjectifRepository $objectifRepository,
        ChartBuilderInterface $chartBuilder,
        Request $request
    ): Response {
        $userId = $this->getUser()->getId();
        $userSexe = $this->getUser()->getSexe();
        $userTaille = intval($this->getUser()->getTaille());
        $userPoids = intval($this->getUser()->getPoids());
        $userAge = $this->common->calculerAge($this->getUser()->getNaissance());
        $userDataHistorique = $this->common->getUserData($historiqueRepository, $userId);
        $userDataObjectif = $this->common->getUserData($objectifRepository, $userId);
        $domaine = 'Activité physique';
        $activity = 'nombre de pas';
        $default = 7;
        $pasEffectuesDefault = $this->common->getDataSurDerniersJours($userDataHistorique, $domaine, $activity, $default);
        $objectifPasDefault = $this->common->getDataSurDerniersJours($userDataObjectif, $domaine, $activity, $default);
        $label = 'Nombre de pas effectués 🚶🏽';
        $secondLabel = 'Objectif de pas 🎯';
        $view = 'board/index.html.twig';


        /**
         * récupérer les données sur les pas
         */

        $data = $this->calculerMesures(
            $userDataHistorique,
            $userDataObjectif,
            $userSexe,
            $userPoids,
            $userTaille,
            $userAge,
            $historiqueRepository,
            $objectifRepository
        );

        $form = $this->createForm(SelectType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $periode = $form->getData()['periode'];
            $visualisation = $form->getData()['visualisation'];
            switch ($visualisation) {
                case 'linear':
                    $parameters = $this->commonDraw->drawLineWhenIsSelected(
                        $chartBuilder,
                        $periode,
                        $userDataHistorique,
                        $userDataObjectif,
                        $form,
                        $domaine,
                        $activity,
                        $label,
                        $secondLabel,
                        $data
                    );
                    return $this->render($view, $parameters);
                    break;
                case 'bar':
                    $parameters = $this->commonDraw->drawBarWhenIsSelected(
                        $chartBuilder,
                        $periode,
                        $userDataHistorique,
                        $userDataObjectif,
                        $form,
                        $domaine,
                        $activity,
                        $label,
                        $secondLabel,
                        $data
                    );
                    return $this->render($view, $parameters);
                    break;
                case 'mixed':
                    $parameters = $this->commonDraw->drawMixedWhenIsSelected(
                        $chartBuilder,
                        $periode,
                        $userDataHistorique,
                        $userDataObjectif,
                        $form,
                        $domaine,
                        $activity,
                        $label,
                        $secondLabel,
                        $data
                    );
                    return $this->render($view, $parameters);
                    break;
            }
        }

        return $this->render($view, [
            'datas' => $data,
            'form' => $form->createView(),
            'chart_pas' => $this->commonDraw->draw($chartBuilder, LINEAR_CHART, $pasEffectuesDefault, $label),
            'chart_pas_objectif' => $this->commonDraw->draw($chartBuilder, LINEAR_CHART, $pasEffectuesDefault, 'Nombre de pas effectués 🚶🏽', true, $objectifPasDefault, $secondLabel),
        ]);
    }



    #[Route('/board/poids', name: 'app_board_poids')]
    public function affichagePoids(){
        $userSexe = $this->getUser()->getSexe();
        $userTaille = intval($this->getUser()->getTaille());
        $userPoids = intval($this->getUser()->getPoids());
        $userAge = $this->common->calculerAge($this->getUser()->getNaissance());

        $imc = $this->common->calculateIndiceMasseCorporelle($userPoids, $userTaille);
        $img = $this->common->calculateIndiceMasseGrasse($imc, $userAge, $userSexe);
        $interpretationImc = $this->common->interpretationIndiceMasseCorporel($imc);
        $interpretationImg = $this->common->interpretationIndiceMasseGrasse($img, $userSexe);

        return $this->render('board/poids.html.twig', [
            'imc' => $imc,
            'img' => $img,
            'interpretationImc' => $interpretationImc,
            'interpretationImg' => $interpretationImg,
        ]);
    }


    /**
     * calcul les valeurs d'affichages
     */
    private function calculerMesures(array $userDataHistorique, array $userDataObjectif, string $userSexe, float $userPoids, float $userTaille, int $userAge, $historiqueRepository, $objectifRepository): array
    {
        $pasEffectifRecover = $this->common->getDerniereDonneeSemaineCourante($userDataHistorique, 'Activité physique', 'nombre de pas', true);
        $pasEffectif = array_sum(array_values($pasEffectifRecover));
        $pasObjectifRecover = $this->common->getDerniereDonneeSemaineCourante($userDataObjectif, 'Activité physique', 'nombre de pas');
        $objectif_id = $objectifRepository->findBy(['valeur' => $pasObjectifRecover]);
        if (!$objectif_id) {
            return [];
        }


        $data = [];

        // Calcul des pas effectués et objectifs
        $data['Pas'] = [
            'effectif' => $pasEffectif,
            'objectif' => $pasObjectifRecover,
            'image' => '/assets/images/mdi_foot-print.svg',
            'unite' => 'pas',
            'objectif_id' => $objectif_id[0]->getId(),
        ];

        // Calcul de la distance parcourue
        $distanceParcourueEffectif = $this->common->calculDistanceParcourue(intval($pasEffectif));
        $distanceParcourueObjectif = $this->common->calculDistanceParcourue(intval($pasObjectifRecover));
        $data['Distance Parcourue'] = [
            'effectif' => $distanceParcourueEffectif,
            'objectif' => $distanceParcourueObjectif,
            'image' => '/assets/images/distance.svg',
            'unite' => 'km',
        ];

        // Calcul de la dépense énergétique totale et des calories brûlées
        $userDepenseEnergetiqueTotaleEffectif = $this->common->calculDepenseEnergetiqueTotale($distanceParcourueEffectif, $userSexe, $userPoids, $userTaille, $userAge);
        $userDepenseEnergetiqueTotaleObjectif = $this->common->calculDepenseEnergetiqueTotale($distanceParcourueObjectif, $userSexe, $userPoids, $userTaille, $userAge);
        $caloriesBruleesEffectif = $this->common->calculCaloriesBrulees($userDepenseEnergetiqueTotaleEffectif);
        $caloriesBruleesObjectif = $this->common->calculCaloriesBrulees($userDepenseEnergetiqueTotaleObjectif);
        $data['Calories Brulées'] = [
            'effectif' => $caloriesBruleesEffectif,
            'objectif' => $caloriesBruleesObjectif,
            'unite' => 'kcal',
            'image' => '/assets/images/icon-park-solid_sport.svg'
        ];

        return $data;
    }

}
