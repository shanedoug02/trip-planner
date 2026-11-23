<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

#[Signature('app:status')]
#[Description('Check that the web server and database are working')]
class AppStatus extends Command
{
    public function handle(): int
    {
        // Run both checks first so both lines always print, even if the first fails
        $webOk = $this->checkWebServer();
        $databaseOk = $this->checkDatabase();

        return $webOk && $databaseOk ? self::SUCCESS : self::FAILURE;
    }

    private function checkWebServer(): bool
    {
        $url = config('app.url');

        try {
            $response = Http::timeout(3)
                ->withOptions(['force_ip_resolve' => 'v4']) // skip the slow IPv6 lookup for Herd's .test domains
                ->get($url.'/up');
            $ok = $response->successful();
            $result = $ok ? '✅ up' : "❌ HTTP {$response->status()}";
        } catch (Throwable $e) {
            $ok = false;
            $result = '❌ '.$e->getMessage();
        }

        $this->components->twoColumnDetail("Web server ({$url})", $result);

        return $ok;
    }

    private function checkDatabase(): bool
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        try {
            DB::connection()->getPdo(); // Get PHP Data Objects
            $ok = true;
            $result = '✅ connected';
        } catch (Throwable $e) {
            $ok = false;
            $result = '❌ '.$e->getMessage();
        }

        $this->components->twoColumnDetail("Database ({$connection} / {$database})", $result);

        return $ok;
    }
}
