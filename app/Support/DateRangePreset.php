<?php

namespace App\Support;

enum DateRangePreset: string
{
    case Today = 'Today';

    case Yesterday = 'Yesterday';

    case CurrentWeek = 'CurrentWeek';

    case CurrentMonth = 'CurrentMonth';

    case LastWeek = 'LastWeek';

    case LastMonth = 'LastMonth';

    case CurrentYear = 'CurrentYear';

    case LastYear = 'LastYear';
}
