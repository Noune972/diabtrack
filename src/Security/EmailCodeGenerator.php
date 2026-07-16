<?php

namespace App\Security;

class EmailCodeGenerator
{
    public function generate(): string
    {
        return (string) random_int(100000, 999999);
    }
}