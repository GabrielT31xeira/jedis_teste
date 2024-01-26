<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class Cacher
{
    public function __construct(public string $store = 'file')
    {
    }

    # Seta os valores de cache
    public function setCached($key, $value)
    {
        $cachedData = Cache::store($this->store)->put($key, $value);
    }
    # Busca os valores de cache
    public function getCached($key)
    {
        $cachedData =   Cache::store($this->store)->get($key);
        if ($cachedData) {
            return json_decode($cachedData);
        }
    }
    # Remove os valores em cache
    public function removeCached($key)
    {
        Cache::store($this->store)->forget($key);
    }
}
