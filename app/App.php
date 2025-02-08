<?php

// In this file are the main functions

declare(strict_types = 1);

function getTransactionFiles(string $dirPath): array {
    $files = [];

    foreach(scandir($dirPath) AS $file) {
        if(is_dir($file)) {
            continue;
        }
        $files[] = $dirPath . $file;
    }
    
    return $files;
}

// read from each file and extract it
function getTransactions(string $fileName, ?callable $transactionHandler = null): array {
    if (! file_exists($fileName)) {
        trigger_error('File '. $fileName . ' does not exists.', E_USER_ERROR);
    }

    // open file
    $file = fopen($fileName, 'r');

    fgetcsv($file);

    // read the file line by line
    $transactions = [];

    while (($transaction = fgetcsv($file)) !== false) {
        if ($transactionHandler !== null) {
            $transaction = $transactionHandler($transaction);
        }

        $transactions[] = $transaction;
    }

    return $transactions;
}

function extractTransactions(array $transactionRows): array {
    [$date, $checkNumber, $description, $amount] = $transactionRows;

    $amount = (float) str_replace(['$', ','], '', $amount);

    return [
        'date' => $date,
        'checkNumber' => $checkNumber,
        'description' => $description,
        'amount' => $amount,
    ];
}

function calculateTotals(array $transactions): array {
    $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

    foreach($transactions as $transaction) {
        $totals['netTotal'] += $transaction['amount'];

        if ($transaction['amount'] >= 0) {
            $totals['totalIncome'] += $transaction['amount'];
        } else {
            $totals['totalExpense'] += $transaction['amount'];
        }
    }

    return $totals;
}