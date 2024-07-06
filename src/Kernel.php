<?php

namespace App;


use App\Trait\ChangeTimeZoneTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    use ChangeTimeZoneTrait;

    public function __construct(string $environment, bool $debug)
    {
        $this->changeTimeZone($_ENV['TIME_ZONE']);

        parent::__construct($environment, $debug);
    }
}