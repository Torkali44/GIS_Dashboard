# TCPDF fonts (project-owned)

TrueType sources used by contract PDF generation:

- `arial.ttf`
- `arialbd.ttf`

Generated TCPDF definitions (required at runtime — do not delete):

- `arial.php` / `arial.z` / `arial.ctg.z`
- `arialbd.php` / `arialbd.z` / `arialbd.ctg.z`

Core Helvetica metric files (`helvetica*.php`) are copied here so TCPDF can
resolve footer fonts from the same directory when `K_PATH_FONTS` points at
`resources/fonts`.

Regenerate / restore after a fresh install:

```bash
php artisan tcpdf:ensure-fonts
```

or:

```bash
composer run tcpdf-fonts
```

Do not rely on copying these files into `vendor/tecnickcom/tcpdf/fonts`.
