<?php

namespace App\Support;

/**
 * Registers the project-owned TCPDF font directory.
 *
 * Font definition files (*.php, *.z, *.ctg.z) live under resources/fonts
 * so PDF generation does not depend on files inside vendor/tecnickcom/tcpdf.
 */
final class TcpdfFonts
{
    /**
     * Point TCPDF at resources/fonts before any TCPDF instance is created.
     */
    public static function registerPath(): void
    {
        if (defined('K_PATH_FONTS')) {
            return;
        }

        $path = resource_path('fonts');
        if (! is_dir($path)) {
            return;
        }

        define('K_PATH_FONTS', rtrim(str_replace('\\', '/', $path), '/').'/');
    }

    /**
     * Ensure Arial / Arial Bold TCPDF definitions exist under resources/fonts.
     *
     * Regenerates from the project TTF sources when missing, and copies the
     * small core Helvetica metric files from the TCPDF package (used only in
     * the contract footer) into the same project directory.
     *
     * @return list<string> Human-readable status lines
     */
    public static function ensureDefinitions(): array
    {
        self::registerPath();

        $outDir = resource_path('fonts');
        $messages = [];

        if (! is_dir($outDir)) {
            mkdir($outDir, 0775, true);
        }

        $required = [
            'arial.php', 'arial.z', 'arial.ctg.z',
            'arialbd.php', 'arialbd.z', 'arialbd.ctg.z',
        ];

        $missing = array_filter($required, fn (string $file) => ! is_file($outDir.DIRECTORY_SEPARATOR.$file));

        if ($missing !== []) {
            $tool = base_path('vendor/tecnickcom/tcpdf/tools/tcpdf_addfont.php');
            if (! is_file($tool)) {
                throw new \RuntimeException(
                    'TCPDF font tool not found. Run composer install, then regenerate fonts.'
                );
            }

            foreach (['arial.ttf', 'arialbd.ttf'] as $ttf) {
                $ttfPath = $outDir.DIRECTORY_SEPARATOR.$ttf;
                if (! is_file($ttfPath)) {
                    throw new \RuntimeException("Missing TrueType font: resources/fonts/{$ttf}");
                }

                $cmd = sprintf(
                    '%s %s -i %s -o %s',
                    escapeshellarg(PHP_BINARY),
                    escapeshellarg($tool),
                    escapeshellarg($ttfPath),
                    escapeshellarg($outDir)
                );

                $output = [];
                $exit = 0;
                exec($cmd, $output, $exit);
                if ($exit !== 0) {
                    throw new \RuntimeException(
                        "Failed to convert {$ttf} for TCPDF:\n".implode("\n", $output)
                    );
                }

                $messages[] = "Generated TCPDF definitions from resources/fonts/{$ttf}";
            }
        } else {
            $messages[] = 'Arial TCPDF definitions already present in resources/fonts';
        }

        $vendorFonts = base_path('vendor/tecnickcom/tcpdf/fonts');
        foreach (['helvetica.php', 'helveticab.php', 'helveticai.php', 'helveticabi.php'] as $core) {
            $dest = $outDir.DIRECTORY_SEPARATOR.$core;
            $src = $vendorFonts.DIRECTORY_SEPARATOR.$core;
            if (is_file($dest)) {
                continue;
            }
            if (! is_file($src)) {
                throw new \RuntimeException("Missing TCPDF core font metric: {$core}");
            }
            if (! @copy($src, $dest)) {
                throw new \RuntimeException("Unable to copy {$core} into resources/fonts");
            }
            $messages[] = "Copied {$core} into resources/fonts";
        }

        return $messages;
    }
}
