<?php

namespace Birdperson;

class Server
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    final public function id(): int
    {
        return $this->id;
    }
}