
CREATE TABLE USERS (
    ID NUMBER PRIMARY KEY,
    NAME VARCHAR2(100) NOT NULL,
    EMAIL VARCHAR2(100) UNIQUE NOT NULL,
    PASSWORD VARCHAR2(255) NOT NULL,
    ROLE VARCHAR2(20) NOT NULL,
    CREATED_AT DATE DEFAULT SYSDATE
);

CREATE TABLE NEWS_ITEMS (
    ID NUMBER PRIMARY KEY,
    NEWS_TITLE VARCHAR2(255) NOT NULL,
    AUTHOR_NAME VARCHAR2(100) NOT NULL,
    NEWS_DESCRIPTION CLOB,
    CATEGORY VARCHAR2(50),
    STATUS VARCHAR2(50) DEFAULT 'Pending_Channel',
    "date" DATE DEFAULT SYSDATE,
    IMAGE VARCHAR2(255),
    TARGET_CHANNEL NUMBER,
    ADMIN_FEEDBACK VARCHAR2(1000)
);

CREATE TABLE COMMENTS (
    ID NUMBER PRIMARY KEY,
    ARTICLE_ID NUMBER,
    USER_NAME VARCHAR2(100),
    COMMENT_TEXT VARCHAR2(4000),
    STATUS VARCHAR2(20) DEFAULT 'Pending',
    "date" DATE DEFAULT SYSDATE
);

CREATE TABLE CHANNEL_AUTHORS (
    CHANNEL_ID NUMBER NOT NULL,
    AUTHOR_ID NUMBER NOT NULL,
    PRIMARY KEY (CHANNEL_ID, AUTHOR_ID)
);

CREATE TABLE AUDIT_LOGS (
    ID NUMBER PRIMARY KEY,
    ACTION VARCHAR2(50),
    DETAILS VARCHAR2(1000),
    CREATED_AT DATE DEFAULT SYSDATE
);

CREATE TABLE INBOX_MESSAGES (
    ID NUMBER PRIMARY KEY,
    USER_ID NUMBER,
    MESSAGE VARCHAR2(4000),
    IS_READ NUMBER DEFAULT 0,
    CREATED_AT DATE DEFAULT SYSDATE
);

CREATE TABLE CHANNEL_TASKS (
    ID NUMBER PRIMARY KEY,
    CHANNEL_ID NUMBER NOT NULL,
    AUTHOR_ID NUMBER NOT NULL,
    TOPIC VARCHAR2(255) NOT NULL,
    RESOURCES VARCHAR2(4000),
    DEADLINE VARCHAR2(50),
    STATUS VARCHAR2(50) DEFAULT 'Pending',
    CREATED_AT DATE DEFAULT SYSDATE
);


-- 4. PL/SQL BUSINESS LOGIC (TRIGGERS)


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
            v_details := '⏳ Author Submitted: "' || :NEW.NEWS_TITLE || '" -> Waiting for News Channel Approval.';
        ELSIF v_status = 'Pending_Admin' THEN
            v_details := '✅ Channel Approved: "' || :NEW.NEWS_TITLE || '" -> Forwarded to Super Admin.';
        ELSIF LOWER(v_status) = 'published' THEN
            v_details := '✅✅ Super Admin Published: "' || :NEW.NEWS_TITLE || '" is now live!';
        ELSIF v_status = 'Modification_Required' THEN
            v_details := '✏️ Modification Requested: "' || :NEW.NEWS_TITLE || '" sent back to Author.';
        ELSIF v_status = 'Rejected_Admin' OR v_status = 'Rejected_Permanent' THEN
            v_details := '❌ Rejected: "' || :NEW.NEWS_TITLE || '" was rejected.';
        ELSE
            v_details := '🔄 Status updated to ' || v_status || ' for "' || :NEW.NEWS_TITLE || '"';
        END IF;
    END IF;

    INSERT INTO AUDIT_LOGS (ID, ACTION, DETAILS) 
    VALUES ((SELECT NVL(MAX(ID), 0) + 1 FROM AUDIT_LOGS), v_action, v_details);
END;
/


-- 5. PL/SQL STORED PROCEDURES


CREATE OR REPLACE PROCEDURE REJECT_NEWS_PROC (
    p_article_id IN NUMBER,
    p_feedback IN VARCHAR2
) AS
BEGIN
    UPDATE NEWS_ITEMS 
    SET STATUS = 'Rejected_Admin', 
        ADMIN_FEEDBACK = p_feedback 
    WHERE ID = p_article_id;
    
    INSERT INTO AUDIT_LOGS (ID, ACTION, DETAILS) 
    VALUES ((SELECT NVL(MAX(ID), 0) + 1 FROM AUDIT_LOGS), 'REJECT', 'Admin rejected article ID ' || p_article_id || ' with feedback.');
    
    COMMIT;
END;
/

CREATE OR REPLACE PROCEDURE publish_news_proc (
    p_article_id IN NUMBER
) AS
BEGIN
    UPDATE NEWS_ITEMS 
    SET STATUS = 'Published',
        "date" = SYSDATE,
        ADMIN_FEEDBACK = NULL
    WHERE ID = p_article_id;
    
    INSERT INTO AUDIT_LOGS (ID, ACTION, DETAILS)
    VALUES ((SELECT NVL(MAX(ID), 0) + 1 FROM AUDIT_LOGS), 'PUBLISH', 'Article ID ' || p_article_id || ' successfully published by Super Admin.');
    
    COMMIT;
END;
/


-- 6. PL/SQL FUNCTIONS


CREATE OR REPLACE FUNCTION GET_TOTAL_PUBLISHED_NEWS 
RETURN NUMBER IS
    v_count NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_count FROM NEWS_ITEMS WHERE LOWER(STATUS) = 'published';
    RETURN v_count;
END;
/
