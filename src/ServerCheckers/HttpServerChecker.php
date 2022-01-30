<?php

namespace Innocenzi\Vite\ServerCheckers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Innocenzi\Vite\Vite;

final class HttpServerChecker implements ServerChecker
{
    public function ping(string $url, int $timeout): bool
    {
        try {
            $url = Str::of($url)->finish('/')->append(Vite::CLIENT_SCRIPT_PATH);

            return Http::withOptions([
                'connect_timeout' => $timeout,
                'verify' => false,
            ])->get($url)->successful();
        } catch (\Throwable) {
        }

        return false;
    }
}
