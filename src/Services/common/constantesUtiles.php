<?php

namespace App\Services;

use Symfony\UX\Chartjs\Model\Chart;

define('LINEAR_CHART', Chart::TYPE_LINE);
define('BAR_CHART', Chart::TYPE_BAR);
define('AVERAGE_LENGHT_STEP', 0.75); // longueur moyenne d'un pas pour adulte en metre
define('AVERAGE_METABOLIC_COST', 1.1); // facteur moyen cout métabolique
define('CALORIES_CONVERSION_FACTOR', 0.239); // facteur de conversion des calories

define('IMC_CORPULENCE_DENUTRITION', 16.5);
define('IMC_CORPULENCE_MAIGRE', 18.5);
define('IMC_CORPULENCE_NORMAL', 24.9);
define('IMC_CORPULENCE_SURPOIDS', 29.9);
define('IMC_CORPULENCE_OBESITE', 30);
define('IMC_CORPULENCE_OBESITE_SEVERE', 35);
define('IMC_CORPULENCE_OBESITE_MORBIDE', 40);

define('IMG_HOMME_MAIGRE', 15);
define('IMG_HOMME_NORMALE', 20);
define('IMG_FEMME_MAIGRE', 25);
define('IMG_FEMME_NORMALE', 30);


const  SEUILS_IMC = array(
        IMC_CORPULENCE_DENUTRITION,
        IMC_CORPULENCE_MAIGRE,
        IMC_CORPULENCE_NORMAL,
        IMC_CORPULENCE_SURPOIDS,
        IMC_CORPULENCE_OBESITE,
        IMC_CORPULENCE_OBESITE_SEVERE,
        IMC_CORPULENCE_OBESITE_MORBIDE
);

const ANALYSES = array(
        '👀 Dénutrition : 
        👉 Consulter un médecin',
        '👀 État de maigreur : votre poids est insuffisant et 
        peut occasionner certains risques pour la santé.
        👉 Consulter un médecin afin de vous aider à déterminer la cause de cette maigreur',
        '✅ Corpulence normale : Votre poids 
        n\'augmente pas les risques pour votre santé.',
        '⚠️ Surpoids : votre excès de poids peut occasionner 
        certains risques pour votre santé.
        👉 Pratiquer une activité physique régulière, 30 à 60 minutes par jour, 3-5 jours par semaine.
        ❌ Éviter alcool, tabac et autres substances toxiques',
        '☣️ Vous etes en obésité : vous présenté un 
        risque accru de développer certaines maladies.
        👉 Consulter un médecin et un diététicien',
        '☣️ Obésité morbide : vous présenté un 
        risque accru de développer certaines maladies.
        👉 Consulter un médecin et un diététicien',
        '☣️ Obésité morbide : vous présenté un 
        risque accru de développer certaines maladies.
        👉 Consulter un médecin et un diététicien'
);