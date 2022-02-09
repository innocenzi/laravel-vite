<?php

namespace Innocenzi\Vite\HeartbeatCheckers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Innocenzi\Vite\Vite;

final class HttpHeartbeatChecker implements HeartbeatChecker
{
    public function ping(string $url, int $timeout): bool
    {
        try {
            $url = Str::of($url)->finish('/')->append(Vite::CLIENT_SCRIPT_PATH);

            $request = Http::withOptions([
                'connect_timeout' => $timeout,
                'verify' => false,
            ]);

            if ((int) Str::substr(app()->version(), 0, 1) >= 9) {
                $request->connectTimeout($timeout);
            }

            return $request->get($url)->successful();
        } catch (\Throwable) {
        }

        return false;
    }
}
