<?php

namespace App\Enums;

enum LoanType: string
{
    case Personal = 'personal';
    case Education = 'education';
    case Home = 'home';
    case Car = 'car';
    case Business = 'business';

}
