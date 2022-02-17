<?php

namespace App\Enums;

enum LoanStatus: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Pending = 'pending';

}
