<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class UniqueUserEmail extends Constraint
{
    public string $message = 'L\'adresse email "{{ value }}" est déjà utilisée.';
}