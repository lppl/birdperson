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

    final public function currentTimeFormat(string $format): string
    {
        return $this->currentTime->format($format);
    }

    final public function timeAfter(int $seconds): self
    {
        $diff = DateInterval::createFromDateString(sprintf('%s seconds', $seconds));
        return new self($this->currentTime->add($diff)->format(self::FORMAT));
    }

    public function __toString(): string
    {
        return $this->currentTime();
    }
}