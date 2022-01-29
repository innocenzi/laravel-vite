<?php

namespace Innocenzi\Vite\ServerCheckers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final class HttpServerChecker implements ServerChecker
{
    public function ping(string $url, int $timeout): bool
    {
        try {
            Http::withOptions([
                'connect_timeout' => $timeout,
                'verify' => false,
            ])->get($url);
        } catch (ConnectionException) {
            return false;
        } catch (\Throwable) {
        }

        return true;
    }
}
