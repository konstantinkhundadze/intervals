<?php

/**
 * Entity to work with Intervals
 */
class Interval
{

    /** @var @var int */
    protected $start;

    /** @var @var int */
    protected $end;

    /** @var @var float */
    protected $price;

    public function getStart(): int
    {
        return $this->start;
    }

    public function setStart(int $value): self
    {
        $this->start = $value;
        return $this;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function setEnd(int $value): self
    {
        $this->end = $value;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $value): self
    {
        $this->price = $value;
        return $this;
    }

    /**
     * @return string format '1-5:15'
     *          `start`-`end`:`price`
     */
    public function __toString()
    {
        return $this->getStart().'-'.$this->getEnd().':'.$this->getPrice();
    }
}
