<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class CalculService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function calculTTC(int $prix, int $tva): int
    {
        $TTC = $prix + ($prix * $tva / 100);
        $this->logger->error($TTC);
        return $TTC;
    }
}
