<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\DB::unprepared("
        CREATE OR REPLACE TRIGGER NEWS_AUDIT_TRG
        AFTER INSERT OR UPDATE OR DELETE ON NEWS_ITEMS
        FOR EACH ROW
        DECLARE
            v_action VARCHAR2(20);
            v_details VARCHAR2(1000);
            v_status VARCHAR2(100);
        BEGIN
            IF DELETING THEN
                v_action := 'DELETE';
                v_details := '❌ Article Deleted: ' || :OLD.NEWS_TITLE;
            ELSE
                IF INSERTING THEN
                    v_action := 'INSERT';
                ELSE
                    v_action := 'UPDATE';
                END IF;
                
                v_status := :NEW.STATUS;
                
                IF v_status = 'Pending_Channel' THEN
                    v_details := '⏳ Author Submitted: \"' || :NEW.NEWS_TITLE || '\" -> Waiting for News Channel Approval.';
                ELSIF v_status = 'Pending_Admin' THEN
                    v_details := '✅ Channel Approved: \"' || :NEW.NEWS_TITLE || '\" -> Forwarded to Super Admin.';
                ELSIF LOWER(v_status) = 'published' THEN
                    v_details := '✅✅ Super Admin Published: \"' || :NEW.NEWS_TITLE || '\" is now live!';
                ELSIF v_status = 'Modification_Required' THEN
                    v_details := '✏️ Modification Requested: \"' || :NEW.NEWS_TITLE || '\" sent back to Author.';
                ELSIF v_status = 'Rejected_Admin' OR v_status = 'Rejected_Permanent' THEN
                    v_details := '❌ Rejected: \"' || :NEW.NEWS_TITLE || '\" was rejected.';
                ELSE
                    v_details := '🔄 Status updated to ' || v_status || ' for \"' || :NEW.NEWS_TITLE || '\"';
                END IF;
            END IF;

            INSERT INTO AUDIT_LOGS (ACTION, DETAILS) 
            VALUES (v_action, v_details);
        END;
    ");
    
    // Clear old logs so it looks clean for the presentation
    Illuminate\Support\Facades\DB::statement("DELETE FROM AUDIT_LOGS");
    
    echo "Trigger updated successfully and old logs cleared!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
