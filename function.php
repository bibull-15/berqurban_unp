<?php
// functions.php

define('PARTICIPANTS_DB', 'participants.json');
define('DEDUCTIONS_DB', 'deductions.json');
define('REPORT_DB', 'qurban_final_report_1447H.json');

// Function to read data from a JSON file
function read_json($filename) {
    if (!file_exists($filename)) {
        return []; // Return empty array if file doesn't exist
    }
    $json_data = file_get_contents($filename);
    return json_decode($json_data, true);
}

// Function to write data to a JSON file
function write_json($filename, $data) {
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($filename, $json_data);
}

// Function to generate a unique ID
function generate_unique_id($prefix = '') {
    return $prefix . uniqid();
}
?>