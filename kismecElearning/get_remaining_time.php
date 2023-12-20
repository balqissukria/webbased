<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    echo json_encode(['userKey' => null, 'remainingTime' => null]);
    exit();
}

$userKey = md5($_SESSION['user_email'] . $_SESSION['user_password']);

if (isset($_SESSION['remaining_time'][$userKey])) {
    $remainingTime = $_SESSION['remaining_time'][$userKey];

    // Format the remaining time as needed
    $formattedRemainingTime = formatRemainingTime($remainingTime);

    echo json_encode(['userKey' => $userKey, 'remainingTime' => $formattedRemainingTime]);
} else {
    echo json_encode(['userKey' => null, 'remainingTime' => null]);
}

function formatRemainingTime($remainingTime) {
    // Your formatting logic goes here
    return $remainingTime;
}
?>
