<?php

namespace Birdperson;

interface ServerProvider
{
    public function getBestPossibleServer(): Server;
}