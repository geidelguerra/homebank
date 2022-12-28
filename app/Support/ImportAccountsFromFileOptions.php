<?php

namespace App\Support;

class ImportAccountsFromFileOptions
{
    public function __construct(
        private readonly int $dateColumnIndex,
        private readonly int $amountColumnIndex,
        private readonly int $categoryColumnIndex,
        private readonly int $descriptionColumnIndex,
        private readonly int $accountColumnIndex,
        private readonly int $ignoredRows = 0,
        private readonly string $dateTimezone = 'UTC',
    ) {
    }

    public function getIgnoredRows(): int
    {
        return $this->ignoredRows;
    }

    public function getDateTimezone(): string
    {
        return $this->dateTimezone;
    }

    public function getAmountColumnIndex(): int
    {
        return $this->amountColumnIndex;
    }

    public function getDateColumnIndex(): int
    {
        return $this->dateColumnIndex;
    }

    public function getCategoryColumnIndex(): int
    {
        return $this->categoryColumnIndex;
    }

    public function getDescriptionColumnIndex(): int
    {
        return $this->descriptionColumnIndex;
    }

    public function getAccountColumnIndex(): int
    {
        return $this->accountColumnIndex;
    }
}
