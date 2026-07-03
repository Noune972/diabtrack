<?php
namespace App\Enum;

enum ReminderType: string
{
    case INSULINE_LENTE = 'insuline_lente';
    case INSULINE_RAPIDE = 'insuline_rapide';
    case SPORT = 'sport';
    case HEMOGLOBINE_GLYQUEE = 'hemoglobine_glyquee';
    case METFORMINE = 'metformine';
    case GLYCEMIE = 'glycemie';
}