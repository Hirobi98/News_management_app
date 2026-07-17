<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $cols = DB::select("SELECT COLUMN_NAME FROM ALL_TAB_COLUMNS WHERE TABLE_NAME = 'NEWS_ITEMS'");
    foreach($cols as $col) {
        echo $col->column_name . "\n";
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}
