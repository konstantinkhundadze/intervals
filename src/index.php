<?php

echo "Example 1 \r\n";
$example1 = [
    '1-10:15',
    '5-20:15',
    '2-8:45',
    '9-10:45',
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

/**
 * Provides working with the Intervals
 * @todo Interface with methods getResult, add
 * @todo Providers or Drivers to work with DB, files or array,
 *       all protected and private methods should be implemented in those Providers
 */
class IntervalService
{
    /** @var array */
    protected $intervals = [];

    /**
     * Get list of all intervals
     * @return string
     */
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

    /**
     * Set new interval and merge with others
     * @param string $value
     * @return IntervalService
     */
    public function add(string $value): self
    {
        $interval = $this->convertToInterval($value);
        $this
            ->cleanUp($interval)
            ->cropNearest($interval)
            ->mergeNearest($interval)
            ->postCleanUp();
        $this->intervals[] = $interval;
        return $this;
    }

    /**
     * Remove all interval between new one
     * @param Interval $interval
     * @return IntervalService
     */
    protected function cleanUp(Interval $interval): self
    {
        foreach ($this->intervals as $k => $v) {
            if($v->getStart() >= $interval->getStart() && $v->getEnd() <= $interval->getEnd()) {
                unset($this->intervals[$k]);
            }
        }
        return $this;
    }

    /**
     * Clean intervals list after add new interval
     * @return IntervalService
     */
    protected function postCleanUp(): self
    {
        foreach ($this->intervals as $k => $v) {
            if($v->getStart() > $v->getEnd()) {
                unset($this->intervals[$k]);
            }
        }
        return $this;
    }

    /**
     * Truncates the intervals that affect the added
     * @param Interval $interval
     * @return IntervalService
     */
    protected function cropNearest(Interval $interval): self
    {
        $newIntervals = [];
        foreach ($this->intervals as $k => $v) {
            if($v->getStart() <= $interval->getStart() && $v->getEnd() >= $interval->getStart()) {
                    if ($v->getEnd() > $interval->getEnd()) {
                        $newIntervals[] = (new Interval())
                            ->setStart($interval->getEnd()+1)
                            ->setEnd($v->getEnd())
                            ->setPrice($v->getPrice());
                    }
                    $v->setEnd($interval->getStart()-1);
            } elseif($v->getStart() <= $interval->getEnd() && $v->getEnd() >= $interval->getEnd()) {
                $v->setStart($interval->getEnd()+1);
            }
        }

        foreach ($newIntervals as $new) {
            $this->intervals[] = $new;
        }

        return $this;
    }

    /**
     * If the intervals before or after new added interval,
     * have the same price we should merge them with the new added
     * @param Interval $interval
     * @return IntervalService
     */
    protected function mergeNearest(Interval $interval): self
    {
        foreach ($this->intervals as $k => $v) {
            if ($v->getStart() == $interval->getEnd() + 1 && $v->getPrice() == $interval->getPrice()) {
                $interval->setEnd($v->getEnd());
                unset($this->intervals[$k]);
            } elseif($v->getEnd() == $interval->getStart() - 1 && $v->getPrice() == $interval->getPrice()) {
                $interval->setStart($v->getStart());
                unset($this->intervals[$k]);
            }
        }

        return $this;
    }

    /**
     * Convert string like '1-10:15' (`start`-`end`:`price`) to object Interval
     * @param string $value
     * @return Interval
     */
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