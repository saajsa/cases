<?php
// Test if constants can be loaded
try {
    require_once(__DIR__ . '/config/hearing_constants.php');
    echo "Constants loaded successfully\n";
    
    // Test some functions
    $statuses = hearing_get_all_statuses();
    echo "Number of statuses: " . count($statuses) . "\n";
    
    $classification = hearing_get_temporal_classification(date('Y-m-d'));
    echo "Today's classification: " . $classification . "\n";
    
    echo "All tests passed!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
?>