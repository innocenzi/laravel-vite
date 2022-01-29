<?php

namespace Innocenzi\Vite;

final class Vite
{
    protected array $configs = [];

    public function config(string $name = null): Configuration
    {
        $name ??= config('vite.default');
        
        return $this->configs[$name] ??= new Configuration($name);
    }
}
