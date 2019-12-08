<?php

namespace Birdperson;

class CyclicServerProvider implements ServerProvider
{
    private Clock $clock;
    private int $serverCount;

    public function __construct(Clock $clock, int $serverCount)
    {
        $this->clock = $clock;
        $this->serverCount = $serverCount;
    }

    final public function getBestPossibleServer(): Server
    {
        return new Server(abs($this->clock->currentTimeFormat('i')) % $this->serverCount);
    }

    final public function getServer(int $id): Server
    {
        return new Server($id);
    }


}