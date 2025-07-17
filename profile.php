<?php
// Vulnerable profile.php file for CVE-2020-24914 testing
// This simulates the vulnerable code in QCubed framework

header('Content-Type: text/html; charset=utf-8');

echo "<h1>QCubed Profile Test Page</h1>";

if ($_POST["data"]) {
    $data = $_POST["data"];
    echo "<h2>Processing POST data...</h2>";
    
    // Vulnerable unserialize call - this is the actual vulnerability
    $obj = unserialize($data);
    
    echo "<h3>Deserialized object:</h3>";
    echo "<pre>";
    var_dump($obj);
    echo "</pre>";
    
    // Additional information for debugging
    echo "<h3>POST data received:</h3>";
    echo "<pre>" . htmlspecialchars($data) . "</pre>";
    
} else {
    echo "<p>No POST data received. Send a POST request with 'data' parameter to test the vulnerability.</p>";
    echo "<p>Example:</p>";
    echo "<pre>";
    echo "curl -X POST http://localhost:8080/profile.php \\\n";
    echo "  -H \"Content-Type: application/x-www-form-urlencoded\" \\\n";
    echo "  -d \"data=O:8:\\\"stdClass\\\":1:{s:4:\\\"test\\\";s:10:\\\"test123456\\\";}&action=save&id=1\"";
    echo "</pre>";
}
?> 