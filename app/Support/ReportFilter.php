<?php

namespace App\Support;

use Stringable;

class ReportFilter implements Stringable
{
    private ReportField $field;

    private ReportFilterOperator $operator;

    private mixed $value;

    public function __construct()
    {
    }

    public function getField(): ReportField
    {
        return $this->field;
    }

    public function setField(ReportField $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function getOperator(): ReportFilterOperator
    {
        return $this->operator;
    }

    public function setOperator(ReportFilterOperator $operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function equals(mixed $value): static
    {
        return $this
            ->setOperator(ReportFilterOperator::Equals)
            ->setValue($value);
    }

    public function greaterThan(mixed $value): static
    {
        return $this
            ->setOperator(ReportFilterOperator::GreaterThan)
            ->setValue($value);
    }

    public function lesserThan(mixed $value): static
    {
        return $this
            ->setOperator(ReportFilterOperator::LesserThan)
            ->setValue($value);
    }

    public static function field(ReportField $field): static
    {
        return (new static())->setField($field);
    }

    public function __toString(): string
    {
        return $this->getField()->name.' '.$this->getOperator()->name.' '.(string) $this->getValue();
    }
}
