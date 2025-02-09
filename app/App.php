<?php

// This file contains the core functions for processing transaction files

declare(strict_types = 1);

/**
 * Retrieves all transaction files from the specified directory.
 *
 * @param string $dirPath The path of the directory containing transaction files.
 * @return array An array of file paths for processing.
 */
function getTransactionFiles(string $dirPath): array 
{
    $files = [];

    // Scan the directory for files
    foreach (scandir($dirPath) as $file) {
        // Skip directories and only process actual files
        if (is_dir($dirPath . $file)) {
            continue;
        }

        // Append the full file path to the list
        $files[] = $dirPath . $file;
    }
 
    return $files;
}

/**
 * Reads transactions from a CSV file and returns them as an array.
 *
 * @param string $fileName The path to the transaction file.
 * @param callable|null $transactionHandler Optional function to process each transaction.
 * @return array An array containing extracted transactions.
 */
function getTransactions(string $fileName, ?callable $transactionHandler = null): array 
{
    // Validate file existence before processing
    if (!file_exists($fileName)) {
        trigger_error('File ' . $fileName . ' does not exist.', E_USER_ERROR);
    }

    // Open the CSV file for reading
    $file = fopen($fileName, 'r');

    // Skip the first row (assumed to be headers)
    fgetcsv($file);

    $transactions = [];

    // Read each transaction record line by line
    while (($transaction = fgetcsv($file)) !== false) {
        // Apply the transaction handler if provided (e.g., extractTransactions)
        if ($transactionHandler !== null) {
            $transaction = $transactionHandler($transaction);
        }

        // Add the processed transaction to the list
        $transactions[] = $transaction;
    }

    return $transactions;
}

/**
 * Extracts and normalizes transaction details from a raw CSV row.
 *
 * @param array $transactionRows A single row of transaction data.
 * @return array An associative array with formatted transaction details.
 */
function extractTransactions(array $transactionRows): array 
{
    // Extract transaction details from CSV row
    [$date, $checkNumber, $description, $amount] = $transactionRows;

    // Convert the amount to a float, removing currency symbols and commas
    $amount = (float) str_replace(['$', ','], '', $amount);

    return [
        'date' => $date,
        'checkNumber' => $checkNumber,
        'description' => $description,
        'amount' => $amount,
    ];
}

/**
 * Computes financial summaries from the transaction records.
 *
 * @param array $transactions The array of transactions.
 * @return array An associative array containing net total, total income, and total expenses.
 */
function calculateTotals(array $transactions): array 
{
    // Initialize total values
    $totals = [
        'netTotal' => 0, 
        'totalIncome' => 0, 
        'totalExpense' => 0
    ];

    // Iterate through transactions to calculate totals
    foreach ($transactions as $transaction) {
        $totals['netTotal'] += $transaction['amount'];

        // Determine if the amount is an income or an expense
        if ($transaction['amount'] >= 0) {
            $totals['totalIncome'] += $transaction['amount'];
        } else {
            $totals['totalExpense'] += $transaction['amount'];
        }
    }

    return $totals;
}
