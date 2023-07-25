<?php

namespace App\Services;

use DateInterval;
use DatePeriod;
use DateTime;
use InvalidArgumentException;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;


require_once 'constantesUtiles.php';

class Common {

    /*public function draw(ChartBuilderInterface $chartBuilder, $type, $data, $label='',$isAnotherChart=false, $data2=null,$label_another_chart='')
    {
        
        $chart = $chartBuilder->createChart($type);
        
        if (!$isAnotherChart and !$data2) {
            $chart->setData([
                'labels' => array_keys($data),
                'datasets' => [
                    [
                        'label' => $label,
                        'backgroundColor' => '#FF9C33',
                        'borderColor' => '#FF9C33',
                        'data' => array_values($data),
                        'tension' => 0.3,
                    ],
                ],
            ]);
        }else {
            $chart->setData([
                'labels' => array_keys($data),
                'datasets' => [
                    [
                        'label' => $label,
                        'yAxisId' => 'suivi_nb_pas',
                        'backgroundColor' => '#FF9C33',
                        'borderColor' => '#FF9C33',
                        'data' => array_values($data),
                        'tension' => 0.3,
                    ],
                    [
                        'label' => $label_another_chart,
                        'yAxisId' => 'objectif_nb_pas',
                        'backgroundColor' => '#615FD7',
                        'borderColor' => '#615FD7',
                        'data' => array_values($data2),
                        'tension' => 0.3,
                    ],
                ],
            ]);
            $chart->setOptions([
                'scales' => [
                    'x' => [
                        'grid' => [
                            'display' => false
                        ],
                    ],
                    'y' => [
                        'id' => 'suivi_distance',
                        'position' => 'left',
                        'suggestedMin' => 0,
                        'suggestedMax' => max($data),
                    ],
                    'y2' => [
                        'id' => 'objectif_distance',
                        'position' => 'right',
                        'suggestedMin' => 0,
                        'suggestedMax' => max($data2),
                    ],
                ]
            ]);
        }       
        return $chart;
    }*/



    /**
     * récupération de l'historique de données de l'utilisateur
     */
    public function getUserData($repository, $user)
    {
        $data = $repository->findBy(['user' => $user]);
        // Initialisation du tableau résultat
        $resultat = [];

        // Parcours des objets Historique
        foreach ($data as $singleData) {
            // Extraction des informations nécessaires
            $domaine = $singleData->getDomaine()->getLibelle();
            $libelle = $singleData->getLibelle()->getLabel();
            $unite = $singleData->getLibelle()->getUnit();
            $valeur = $singleData->getValeur();
            $date = $singleData->getCreatedAt()->format('Y-m-d');

            // Si le domaine n'est pas déjà une clé dans le tableau résultat, on l'ajoute
            if (!isset($resultat[$domaine])) {
                $resultat[$domaine] = [];
            }

            // Si le libelle n'est pas déjà une clé dans le tableau correspondant au domaine, on l'ajoute
            if (!isset($resultat[$domaine][$libelle])) {
                $resultat[$domaine][$libelle] = [];
            }

            if (!isset($resultat[$domaine][$libelle])) {
                $resultat[$domaine][$libelle] = ['unite' => $unite];
            }
            

            // On ajoute la valeur à la liste des valeurs du libellé correspondant au domaine
            $resultat[$domaine][$libelle][] = ['date' => $date, 'valeur' => $valeur, 'unite' => $unite];
        }

        // On regroupe les valeurs par date
        foreach ($resultat as &$domaine) {
            foreach ($domaine as &$libelle) {
                $valeursParDate = [];
                foreach ($libelle as $valeur) {
                    $date = $valeur['date'];
                    $valeur = $valeur['valeur'];
                    if (!isset($valeursParDate[$date])) {
                        $valeursParDate[$date] = [];
                    }
                    $valeursParDate[$date][] = $valeur;
                }
                $libelle = $valeursParDate;
            }
        }

        return $resultat;
    }

    public function getUserDataByDomaineAndActivity($repository, $user, $domaine, $activity){
        $data = $this->getUserData($repository, $user);
        return $data[$domaine][$activity];
    }


    public function getDataSurDerniersJours(array $userData, $domaine, $activite, int $jours): array
    {
        $dataActivity = [];
        if (empty($userData)) {
            $userData[$domaine][$activite] = [];
        }
        if (array_key_exists($domaine, $userData) && array_key_exists($activite, $userData[$domaine])) {
            $dates = array_keys($userData[$domaine][$activite]);
            for ($i = $jours - 1; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i day"));
                $dataActivity[$date] = 0;
                if (in_array($date, $dates)) {
                    foreach ($userData[$domaine][$activite][$date] as $pas) {
                        $dataActivity[$date] += $pas;
                    }
                }
            }
        }
        return $dataActivity;
    }

    /**
     *  A tester
     */
    public function getDataSurSemaine(array $userData, $domaine, $activite, $debut, $fin): array
    {
        $dataActivity = [];
        $dates = array_keys($userData[$domaine][$activite]);
        $dateDebut = date_create($debut);
        $dateFin = date_create($fin);
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($dateDebut, $interval, $dateFin);

        foreach ($dateRange as $date) {
            $dateStr = $date->format('Y-m-d');
            $dataActivity[$dateStr] = 0;
            if (in_array($dateStr,
                $dates
            )) {
                foreach ($userData[$domaine][$activite][$dateStr] as $pas) {
                    $dataActivity[$dateStr] += $pas;
                }
            }
        }

        return $dataActivity;
    }


    /**
     * retourne la valeur de l'objectif de la semaine
     */
    public function getDerniereDonneeSemaineCourante(array $userData, $domaine, $activite, $isArray=false)
    {
        $dataActivity = [];
        if (empty($userData)) {
            $userData[$domaine][$activite] = [];
        }
        $dates = array_keys($userData[$domaine][$activite]);
        $today = date('Y-m-d');
        $dayOfWeek = date('N', strtotime($today));
        $startOfWeek = date('Y-m-d', strtotime("-" . ($dayOfWeek - 1) . " days", strtotime($today)));
        $endOfWeek = date('Y-m-d', strtotime("+" . (7 - $dayOfWeek) . " days", strtotime($today)));

        $dateRange = new DatePeriod(date_create($startOfWeek), new DateInterval('P1D'), date_create($endOfWeek));

        foreach ($dateRange as $date) {
            $dateStr = $date->format('Y-m-d');
            if (in_array($dateStr, $dates)) {
                $lastData = end($userData[$domaine][$activite][$dateStr]);
                $dataActivity[$dateStr] = $lastData;
            }
        }
        if ($isArray) {
            return $dataActivity;
        }

        return end($dataActivity);
    }


    /**
     * calculer l'age d'un utilissateur
     */
    public function calculerAge(DateTime $dateNaissance)
    {
        $aujourdhui = new DateTime();
        $age = $aujourdhui->diff($dateNaissance);
        return $age->y;
    }

    /**
     * calcul de la distance parcourue par l'utilisateur selon le nombre de pas effectués
     */
    public function calculDistanceParcourue($nombre_pas)
    {
        if (!is_int($nombre_pas) || $nombre_pas < 0) {
            throw new InvalidArgumentException('Le nombre de pas doit être un entier positif.');
        }

        $distanceParcourue = ($nombre_pas * AVERAGE_LENGHT_STEP) / 1000;
        $distanceParcourueArrondi = round($distanceParcourue, 1);
        return $distanceParcourueArrondi;
    }



    /**
     * calcul de la dépense énergétique de base selon le poids (kg), la taille (cm),
     * l'age (en année)
     */
    public function calculDepenseEnergetiqueBase($sexe, $poids, $taille, $age)
    {
        $validSexe = in_array($sexe, ['homme', 'femme']);
        $validPoids = is_numeric($poids);
        $validTaille = is_numeric($taille);
        $validAge = is_numeric($age);

        if (!($validSexe && $validPoids && $validTaille && $validAge)) {
            throw new InvalidArgumentException('Les valeurs passées en paramètres sont invalides.');
        }

        if ($sexe === 'homme') {
            return 88.362 + (13.397 * $poids) + (4.799 * $taille) - (5.677 * $age);
        }

        return 447.593 + (9.247 * $poids) + (3.098 * $taille) - (4.330 * $age);
    }

    public function calculDepenseEnergetiqueTotale($distance_parcourue, $sexe, $poids, $taille, $age)
    {
        $depenseEnergetiqueBase = 0;
        if ($distance_parcourue <= 0) {
            return $depenseEnergetiqueBase;
        }
        $depenseEnergetiqueDistance = $this->calculDepenseEnergetiqueDistance($distance_parcourue, $poids);
        $depenseEnergetiqueBase = $this->calculDepenseEnergetiqueBase($sexe, $poids, $taille, $age);
        return $depenseEnergetiqueBase + $depenseEnergetiqueDistance;
    }


    /**
     * calculer la dépense énergétique liée à la distance parcourue
     */
    private function calculDepenseEnergetiqueDistance($distance_parcourue, $poids)
    {
        $depenseEnergetiqueDistance = 0;
        if ($distance_parcourue === 0) {
            return $depenseEnergetiqueDistance;
        }
        $depenseEnergetiqueDistance = ($distance_parcourue * AVERAGE_METABOLIC_COST * $poids) / 1000;
        return $depenseEnergetiqueDistance;
    }


    /**
     * calcul du nombre de calories brulées selon la distance parcourue,
     * l'age, du poids, le temps d'activité et la taille
     */
    public function calculCaloriesBrulees($depenseEnergetiqueTotal)
    {
        $caloriesBrulees = $depenseEnergetiqueTotal * CALORIES_CONVERSION_FACTOR;
        return round($caloriesBrulees, 2);
    }
    

    /**
     * calcul de l'IMC
     */
    public function calculateIndiceMasseCorporelle($poids, $taille)
    {
        $convertTaille = $taille / 100;
        if (!is_numeric($poids) || !is_numeric($taille) || $poids <= 0 || $taille <= 0) {
            throw new InvalidArgumentException('Le poids et la taille doivent être des nombres positifs.');
        }
        $imc = $poids / pow($convertTaille, 2);
        return round($imc, 1);
    }


    /**
     * Calcul de l'IMG qui permet de connaître le taux de masse graisseuse contenu dans son 
     * organisme, et ainsi de définir si vous êtes trop maigre ou trop gras. (en pourcentage)
     */
    public function calculateIndiceMasseGrasse($imc, $age, $sexe)
    {
        $validSexe = in_array($sexe, ['homme', 'femme']);
        $validImc = is_numeric($imc);
        $validAge = is_numeric($age);

        if (!($validSexe && $validImc && $validAge)) {
            throw new InvalidArgumentException('Les valeurs passées en paramètres sont invalides.');
        }

        if ($sexe === 'homme') {
            return (1.2 * $imc) + (0.23 * $age) - (10.8 * 1) - 5.4;
        }

        return (1.2 * $imc) + (0.23 * $age) - (10.8 * 0) - 5.4;
    }



    /**
     * méthode interprétant l'IMC calculé
     */
    public function interpretationIndiceMasseCorporel($imc){
        for ($i = 0; $i < count(SEUILS_IMC); $i++) {
            if ($imc < SEUILS_IMC[$i]) {
                return ANALYSES[$i];
            }
        }
    }

    public function interpretationIndiceMasseGrasse($img, $sexe) {
        /**
         * on définie notre tableau de seuils par sexe
         */
        $seuils = [
            'homme' => [IMG_HOMME_MAIGRE, IMG_HOMME_NORMALE],
            'femme' => [IMG_FEMME_MAIGRE, IMG_FEMME_NORMALE],
        ];

        // on récupère les valeurs correspondantes par rapport au sexe
        $imgIndicatorBySex = $seuils[$sexe];

        $analyse = ($img < $imgIndicatorBySex[0]) ? 'Votre taux corporel de graisse montre que vous êtes trop maigre'
        : ($img < $imgIndicatorBySex[1] ? 'Votre taux corporel de graisse montre que vous êtes dans la norme'
        : 'Votre taux corporel de graisse montre que vous avez trop de graisse');

        return $analyse;
    }
}