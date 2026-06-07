<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$row = \App\Models\HomePageSetting::query()->first();
if ($row) {
    echo "ID: " . $row->id . "\n";
    echo "contact_map_embed_url (raw): " . var_export($row->getRawOriginal('contact_map_embed_url'), true) . "\n";
    echo "contact_map_embed_url (attr): " . var_export($row->contact_map_embed_url, true) . "\n";
    echo "\ncurrent() result:\n";
    $current = \App\Models\HomePageSetting::current();
    echo "contact_map_embed_url: " . var_export($current['contact_map_embed_url'], true) . "\n";
} else {
    echo "No record.\n";
}
