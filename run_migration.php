<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$statements = [
    "CREATE TABLE CHANNEL_AUTHORS (CHANNEL_ID NUMBER NOT NULL, AUTHOR_ID NUMBER NOT NULL, PRIMARY KEY(CHANNEL_ID, AUTHOR_ID))",
    "CREATE SEQUENCE SEQ_INBOX_MESSAGES START WITH 1",
    "CREATE TABLE INBOX_MESSAGES (ID NUMBER PRIMARY KEY, USER_ID NUMBER NOT NULL, MESSAGE VARCHAR2(4000), IS_READ NUMBER(1) DEFAULT 0, CREATED_AT DATE DEFAULT SYSDATE)",
    "CREATE OR REPLACE TRIGGER TRG_INBOX_MESSAGES BEFORE INSERT ON INBOX_MESSAGES FOR EACH ROW BEGIN SELECT SEQ_INBOX_MESSAGES.NEXTVAL INTO :new.ID FROM dual; END;"
];

foreach ($statements as $stmt) {
    try {
        DB::statement($stmt);
        echo "Success: $stmt\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
