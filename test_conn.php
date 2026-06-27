<?php
$servername = "localhost";
$username = "news_user";
$password = "news123";
$dbname = "xe";

$tns1 = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$servername)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=" . strtoupper($dbname) . ")))";
$tns2 = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$servername)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=$dbname)))";
$tns3 = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$servername)(PORT=1521))(CONNECT_DATA=(SID=$dbname)))";

try {
    $conn = new PDO("oci:dbname=" . $tns1, $username, $password);
    echo 'Connected TNS1 (SERVICE_NAME=XE)\n';
} catch(PDOException $e) {
    echo "TNS1 failed: " . $e->getMessage() . "\n";
}

try {
    $conn = new PDO("oci:dbname=" . $tns2, $username, $password);
    echo 'Connected TNS2 (SERVICE_NAME=xe)\n';
} catch(PDOException $e) {
    echo "TNS2 failed: " . $e->getMessage() . "\n";
}

try {
    $conn = new PDO("oci:dbname=" . $tns3, $username, $password);
    echo 'Connected TNS3 (SID=xe)\n';
} catch(PDOException $e) {
    echo "TNS3 failed: " . $e->getMessage() . "\n";
}
?>
