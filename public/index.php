<?php

// Enable strict type checking for better type safety
declare(strict_types = 1);

// Define the root directory of the project
$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

// Define paths for different parts of the application
define('APP_PATH', $root . 'app' . DIRECTORY_SEPARATOR); // Application files
define('FILES_PATH', $root . 'transaction_files' . DIRECTORY_SEPARATOR); // Directory containing transaction files
define('VIEWS_PATH', $root . 'views' . DIRECTORY_SEPARATOR); // View templates

// Include necessary application files
require APP_PATH . 'App.php'; // Main functions
require APP_PATH . 'helpers.php'; // Helper functions

// Retrieve a list of transaction files from the directory
$files = getTransactionFiles(FILES_PATH);
?>

<pre>
    <?php print_r($files); ?>
</pre>

<?php
// Initialize an empty transactions array
$transactions = [];

// Loop through each transaction file and extract data
foreach ($files as $file) {
    // Merge transactions from all files into one array
    $transactions = array_merge($transactions, getTransactions($file, 'extractTransactions'));
}

// Calculate total income, expenses, and net total from transactions
$totals = calculateTotals($transactions);

// Debugging: Uncomment this line to print transactions for verification
// print_r($transactions);

// Load the transactions view to display the data
require VIEWS_PATH . 'transactions.php';

?>
