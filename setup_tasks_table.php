<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Drop existing if any
    try {
        Illuminate\Support\Facades\DB::unprepared("DROP TABLE CHANNEL_TASKS CASCADE CONSTRAINTS");
        Illuminate\Support\Facades\DB::unprepared("DROP SEQUENCE CHANNEL_TASKS_SEQ");
    } catch(Exception $e) {}

    // Create table
    Illuminate\Support\Facades\DB::unprepared("
        CREATE TABLE CHANNEL_TASKS (
            ID NUMBER PRIMARY KEY,
            CHANNEL_ID NUMBER NOT NULL,
            AUTHOR_ID NUMBER NOT NULL,
            TOPIC VARCHAR2(255) NOT NULL,
            RESOURCES VARCHAR2(4000),
            DEADLINE VARCHAR2(50),
            STATUS VARCHAR2(50) DEFAULT 'Pending',
            CREATED_AT DATE DEFAULT SYSDATE
        )
    ");
    echo "Table CHANNEL_TASKS created.\n";

    // Create Sequence
    Illuminate\Support\Facades\DB::unprepared("CREATE SEQUENCE CHANNEL_TASKS_SEQ START WITH 1 INCREMENT BY 1");
    echo "Sequence CHANNEL_TASKS_SEQ created.\n";

    // Create Trigger
    Illuminate\Support\Facades\DB::unprepared("
        CREATE OR REPLACE TRIGGER CHANNEL_TASKS_BIR
        BEFORE INSERT ON CHANNEL_TASKS
        FOR EACH ROW
        BEGIN
            :NEW.ID := CHANNEL_TASKS_SEQ.NEXTVAL;
            IF :NEW.CREATED_AT IS NULL THEN
                :NEW.CREATED_AT := SYSDATE;
            END IF;
            IF :NEW.STATUS IS NULL THEN
                :NEW.STATUS := 'Pending';
            END IF;
        END;
    ");
    echo "Trigger CHANNEL_TASKS_BIR created.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
