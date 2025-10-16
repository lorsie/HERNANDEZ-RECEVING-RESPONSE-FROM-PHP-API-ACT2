<?php
header('Content-Type: application/json');

$prices = [
    'chicharon' => 30,
    'turon' => 20,
    'empanada' => 30,
    'barbeque' => 25,
    'dynamite' => 20,
    'halo-halo' => 25
];

$action = $_GET['action'] ?? '';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing JSON input']);
    exit;
}

switch ($action) {
    case 'processOrder':
        $snack = $input['snack'] ?? '';
        $cash = filter_var($input['cash'] ?? null, FILTER_VALIDATE_FLOAT);
        $quantity = filter_var($input['quantity'] ?? null, FILTER_VALIDATE_INT);

        if (!$snack || $cash === false || $quantity === false || $quantity <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input, please fill in all fields correctly.']);
            exit;
        }

        $total_cost = $prices[$snack] * $quantity;

        if ($cash < $total_cost) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient cash.']);
            exit;
        }

        $change = $cash - $total_cost;
        echo json_encode([
            'success' => true,
            'snack' => $snack,
            'total' => $total_cost,
            'change' => $change
        ]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
}