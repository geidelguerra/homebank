<?php

namespace App\Support;

enum TransactionType: string
{
    case Expense = 'Expense';

    case Income = 'Income';
}
