<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // 1. TRIGGER
    Illuminate\Support\Facades\DB::unprepared("
        CREATE OR REPLACE TRIGGER NEWS_AUDIT_TRG
        AFTER INSERT OR UPDATE OR DELETE ON NEWS_ITEMS
        FOR EACH ROW
        DECLARE
            v_action VARCHAR2(20);
            v_title VARCHAR2(255);
        BEGIN
            IF INSERTING THEN
                v_action := 'INSERT';
                v_title := :NEW.NEWS_TITLE;
            ELSIF UPDATING THEN
                v_action := 'UPDATE';
                v_title := :NEW.NEWS_TITLE;
            ELSIF DELETING THEN
                v_action := 'DELETE';
                v_title := :OLD.NEWS_TITLE;
            END IF;

            INSERT INTO AUDIT_LOGS (ACTION, DETAILS) 
            VALUES (v_action, 'Article: ' || v_title || ' was modified.');
        END;
    ");
    echo "Trigger NEWS_AUDIT_TRG created successfully.\n";

    // 2. PROCEDURE
    Illuminate\Support\Facades\DB::unprepared("
        CREATE OR REPLACE PROCEDURE REJECT_NEWS_PROC (
            p_article_id IN NUMBER,
            p_feedback IN VARCHAR2
        ) AS
        BEGIN
            UPDATE NEWS_ITEMS 
            SET STATUS = 'Rejected_Admin', 
                ADMIN_FEEDBACK = p_feedback 
            WHERE ID = p_article_id;
            
            INSERT INTO AUDIT_LOGS (ACTION, DETAILS) 
            VALUES ('REJECT', 'Admin rejected article ID ' || p_article_id || ' with feedback.');
            
            COMMIT;
        END;
    ");
    echo "Procedure REJECT_NEWS_PROC created successfully.\n";

    // 3. FUNCTION
    Illuminate\Support\Facades\DB::unprepared("
        CREATE OR REPLACE FUNCTION GET_TOTAL_PUBLISHED_NEWS 
        RETURN NUMBER IS
            v_count NUMBER;
        BEGIN
            SELECT COUNT(*) INTO v_count FROM NEWS_ITEMS WHERE LOWER(STATUS) = 'published';
            RETURN v_count;
        END;
    ");
    echo "Function GET_TOTAL_PUBLISHED_NEWS created successfully.\n";

    // 4. VIEW
    Illuminate\Support\Facades\DB::unprepared("
        CREATE OR REPLACE VIEW VW_PUBLISHED_NEWS AS
        SELECT n.ID, n.NEWS_TITLE, n.AUTHOR_NAME, n.NEWS_DESCRIPTION, n.CATEGORY, n.STATUS, n.\"date\", n.IMAGE, u.NAME as TARGET_CHANNEL_NAME
        FROM NEWS_ITEMS n
        LEFT JOIN USERS u ON n.TARGET_CHANNEL = u.ID
        WHERE LOWER(n.STATUS) = 'published'
    ");
    echo "View VW_PUBLISHED_NEWS created successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
