<?php

namespace App\Console\Commands;

use App\Support\TcpdfFonts;
use Illuminate\Console\Command;

class EnsureTcpdfFontsCommand extends Command
{
    protected $signature = 'tcpdf:ensure-fonts';

    protected $description = 'Ensure Arial/Arial Bold TCPDF font definitions exist under resources/fonts';

    public function handle(): int
    {
        try {
            foreach (TcpdfFonts::ensureDefinitions() as $line) {
                $this->info($line);
            }
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('TCPDF fonts path: '.resource_path('fonts'));

        return self::SUCCESS;
    }
}
