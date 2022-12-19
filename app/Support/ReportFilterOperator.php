<?php

namespace App\Support;

enum ReportFilterOperator: string
{
    case Equals = 'Equals';

    case GreaterThan = 'GreaterThan';

    case LesserThan = 'LesserThan';
}
