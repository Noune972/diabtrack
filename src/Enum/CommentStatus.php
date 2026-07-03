<?php
namespace App\Enum;

enum CommentStatus: string
{
    case VALID = 'valid';
    case NON_VALID = 'non_valid';
}