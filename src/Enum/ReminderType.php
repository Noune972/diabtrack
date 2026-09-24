<?php

namespace App\Enum;

enum ReminderType: string
{
    // =========================
    // GLYCÉMIE
    // =========================

    case GLYCEMIE = 'glycemie';
    case GLYCEMIE_REVEIL = 'glycemie_reveil';
    case GLYCEMIE_AVANT_REPAS = 'glycemie_avant_repas';
    case GLYCEMIE_APRES_REPAS = 'glycemie_apres_repas';
    case GLYCEMIE_COUCHER = 'glycemie_coucher';


    // =========================
    // INSULINE
    // =========================

    case INSULINE_LENTE = 'insuline_lente';
    case INSULINE_RAPIDE = 'insuline_rapide';
    case INSULINE_REPAS = 'insuline_repas';


    // =========================
    // MÉDICAMENTS
    // =========================

    case METFORMINE = 'metformine';
    case MEDICAMENT = 'medicament';


    // =========================
    // ALIMENTATION
    // =========================

    case PETIT_DEJEUNER = 'petit_dejeuner';
    case DEJEUNER = 'dejeuner';
    case DINER = 'diner';
    case COLLATION = 'collation';
    case HYDRATATION = 'hydratation';


    // =========================
    // ACTIVITÉ PHYSIQUE
    // =========================

    case SPORT = 'sport';
    case MARCHE = 'marche';


    // =========================
    // SUIVI MÉDICAL
    // =========================

    case HEMOGLOBINE_GLYQUEE = 'hemoglobine_glyquee';
    case RENDEZ_VOUS_MEDICAL = 'rendez_vous_medical';
    case PRISE_DE_SANG = 'prise_de_sang';
    case CONTROLE_OPHTALMOLOGIQUE = 'controle_ophtalmologique';
    case CONTROLE_PIEDS = 'controle_pieds';


    // =========================
    // SUIVI PERSONNEL
    // =========================

    case POIDS = 'poids';
    case TENSION = 'tension';
    case JOURNAL_QUOTIDIEN = 'journal_quotidien';


    /**
     * Libellé lisible affiché dans l'interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::GLYCEMIE => 'Mesurer ma glycémie',
            self::GLYCEMIE_REVEIL => 'Glycémie au réveil',
            self::GLYCEMIE_AVANT_REPAS => 'Glycémie avant le repas',
            self::GLYCEMIE_APRES_REPAS => 'Glycémie après le repas',
            self::GLYCEMIE_COUCHER => 'Glycémie avant le coucher',

            self::INSULINE_LENTE => 'Insuline lente',
            self::INSULINE_RAPIDE => 'Insuline rapide',
            self::INSULINE_REPAS => 'Insuline du repas',

            self::METFORMINE => 'Prendre la metformine',
            self::MEDICAMENT => 'Prendre un médicament',

            self::PETIT_DEJEUNER => 'Petit-déjeuner',
            self::DEJEUNER => 'Déjeuner',
            self::DINER => 'Dîner',
            self::COLLATION => 'Collation',
            self::HYDRATATION => 'Boire de l’eau',

            self::SPORT => 'Activité physique',
            self::MARCHE => 'Marche',

            self::HEMOGLOBINE_GLYQUEE => 'Contrôle HbA1c',
            self::RENDEZ_VOUS_MEDICAL => 'Rendez-vous médical',
            self::PRISE_DE_SANG => 'Prise de sang',
            self::CONTROLE_OPHTALMOLOGIQUE => 'Contrôle ophtalmologique',
            self::CONTROLE_PIEDS => 'Contrôle des pieds',

            self::POIDS => 'Contrôle du poids',
            self::TENSION => 'Contrôle de la tension',
            self::JOURNAL_QUOTIDIEN => 'Compléter mon journal',
        };
    }


    /**
     * Icône utilisée dans l'interface.
     */
    public function icon(): string
    {
        return match ($this) {
            self::GLYCEMIE,
            self::GLYCEMIE_REVEIL,
            self::GLYCEMIE_AVANT_REPAS,
            self::GLYCEMIE_APRES_REPAS,
            self::GLYCEMIE_COUCHER => '🩸',

            self::INSULINE_LENTE,
            self::INSULINE_RAPIDE,
            self::INSULINE_REPAS => '💉',

            self::METFORMINE,
            self::MEDICAMENT => '💊',

            self::PETIT_DEJEUNER,
            self::DEJEUNER,
            self::DINER,
            self::COLLATION => '🍽️',

            self::HYDRATATION => '💧',

            self::SPORT => '🏃',
            self::MARCHE => '🚶',

            self::HEMOGLOBINE_GLYQUEE,
            self::PRISE_DE_SANG => '🧪',

            self::RENDEZ_VOUS_MEDICAL => '🩺',
            self::CONTROLE_OPHTALMOLOGIQUE => '👁️',
            self::CONTROLE_PIEDS => '🦶',

            self::POIDS => '⚖️',
            self::TENSION => '❤️',

            self::JOURNAL_QUOTIDIEN => '📋',
        };
    }


    /**
     * Catégorie permettant de regrouper les rappels.
     */
    public function category(): string
    {
        return match ($this) {
            self::GLYCEMIE,
            self::GLYCEMIE_REVEIL,
            self::GLYCEMIE_AVANT_REPAS,
            self::GLYCEMIE_APRES_REPAS,
            self::GLYCEMIE_COUCHER => 'glycemie',

            self::INSULINE_LENTE,
            self::INSULINE_RAPIDE,
            self::INSULINE_REPAS => 'insuline',

            self::METFORMINE,
            self::MEDICAMENT => 'medicament',

            self::PETIT_DEJEUNER,
            self::DEJEUNER,
            self::DINER,
            self::COLLATION,
            self::HYDRATATION => 'alimentation',

            self::SPORT,
            self::MARCHE => 'activite',

            self::HEMOGLOBINE_GLYQUEE,
            self::RENDEZ_VOUS_MEDICAL,
            self::PRISE_DE_SANG,
            self::CONTROLE_OPHTALMOLOGIQUE,
            self::CONTROLE_PIEDS => 'medical',

            self::POIDS,
            self::TENSION,
            self::JOURNAL_QUOTIDIEN => 'suivi',
        };
    }
}