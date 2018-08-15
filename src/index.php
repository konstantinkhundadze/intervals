<?php

echo "Example 1 \r\n";
$example1 = [
    '1-10:15',
    '5-20:15',
    '2-8:45',
    '9-10:45',
    '1-10:15',
];
$service = new  IntervalService();
foreach ($example1 as $v) {
    echo $service->add($v)->getResult()." \r\n";
}

echo "Example 2 \r\n";
$example2 = [
    '1-5:15',
    '20-25:15',
    '4-21:45',
    '3-21:15',
];
$service = new  IntervalService();
foreach ($example2 as $v) {
    echo $service->add($v)->getResult(). " \r\n";
}

class IntervalService
{
    /** @var array */
    protected $intervals = [];

    public function getResult(): string
    {
        usort($this->intervals, function($a, $b) {
            return $a->getStart() - $b->getStart();
        });

        $result = [];
        foreach ($this->intervals as $v) {
            $result[] = '('.$v.')';
        }
        return implode(", ", $result)." \r\n";
    }

    public function add(string $value): self
    {
        $interval = $this->convertToInterval($value);
        $this
            ->cleanUp($interval)
            ->cropNearest($interval);
        $this->intervals[] = $interval;
        return $this;
    }

    protected function cleanUp(Interval $interval): self
    {
        foreach ($this->intervals as $k => $v) {
            if($v->getStart() >= $interval->getStart() && $v->getEnd() <= $interval->getEnd()) {
                unset($this->intervals[$k]);
            }
        }
        return $this;
    }

    protected function cropNearest(Interval $interval): self
    {
        foreach ($this->intervals as $k => &$v) {
            if($v->getStart() <= $interval->getStart() && $v->getEnd() >= $interval->getStart()) {
                $v->setEnd($interval->getEnd()-1);
            } elseif($v->getStart() <= $interval->getEnd() && $v->getEnd() >= $interval->getEnd()) {
                $v->setStart($interval->getStart()+1);
            }
        }
        return $this;
    }

    protected function convertToInterval(string $value): Interval
    {
        $ex1 = explode('-', $value);
        $ex2 = explode(':', $ex1[1]);

        return (new Interval())
            ->setStart((int)$ex1[0])
            ->setEnd((int)$ex2[0])
            ->setPrice((int)$ex2[1])
        ;
    }

}

/** Entity */
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

    public function __toString()
    {
        return $this->getStart().'-'.$this->getEnd().':'.$this->getPrice();
    }
}