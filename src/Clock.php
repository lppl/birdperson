<?php

namespace Birdperson;

use DateTimeImmutable;
use DateInterval;

class Clock
{
    const FORMAT = 'Y-m-d H:i:s';
    private DateTimeImmutable $currentTime;

    public function __construct(?string $currentTime)
    {
        $this->currentTime = $currentTime ? new DateTimeImmutable($currentTime) : new DateTimeImmutable();
    }

    final public function currentTime(): string
    {
        return $this->currentTime->format(self::FORMAT);
    }

    final public function timeAfter(int $seconds): string
    {
        $diff = DateInterval::createFromDateString(sprintf('%s seconds', $seconds));
        return $this->currentTime->add($diff)->format(self::FORMAT);
    }
}