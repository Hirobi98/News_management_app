<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $cols = Illuminate\Support\Facades\DB::select("SELECT COLUMN_NAME, DATA_TYPE, DATA_LENGTH FROM USER_TAB_COLUMNS WHERE TABLE_NAME = 'INBOX_MESSAGES'");
    foreach($cols as $c) {
        $cName = $c->column_name ?? $c->COLUMN_NAME;
        $cType = $c->data_type ?? $c->DATA_TYPE;
        $cLen = $c->data_length ?? $c->DATA_LENGTH;
        echo "$cName : $cType ($cLen)\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
