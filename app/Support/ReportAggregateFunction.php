<?php

namespace App\Support;

enum ReportAggregateFunction: string
{
    case Avg = 'Avg';

    case Count = 'Count';

    case Min = 'Min';

    case Max = 'Max';

    case Sum = 'Sum';
}
