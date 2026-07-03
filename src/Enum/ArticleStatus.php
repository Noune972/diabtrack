<?php
namespace App\Enum;

enum ArticleStatus: string
{
    case DRAFT = 'draft';
    case PUBLIC = 'public';
}