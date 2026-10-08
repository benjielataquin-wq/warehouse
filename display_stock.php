<?php
require_once 'auth_check.php';
header('Content-Type: application/json');
include_once "conn.php";

$landlord_id = intval($_GET['landlord_id'] ?? 0);
$bh_id = intval($_GET['bh_id'] ?? 0);
$room_id = intval($_GET['room_id'] ?? 0);

try {
    $sql = "SELECT 
                r.*, 
                b.name as bh_name,
                r.availability_status as status
            FROM rooms r
            JOIN boarding_houses b 
                ON r.boarding_house_id = b.boarding_house_id";
    
    $params = [];
    $where = [];
    
    if ($room_id > 0) {
        $where[] = "r.room_id = ?";
        $params[] = $room_id;
    } elseif ($landlord_id > 0) {
        $where[] = "b.landlord_ID = ?";
        $params[] = $landlord_id;
    } elseif ($bh_id > 0) {
        $where[] = "r.boarding_house_id = ?";
        $params[] = $bh_id;
        // Hide occupied rooms from tenant browsing
        $where[] = "r.availability_status != 'occupied'";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY b.name, r.room_type ASC";
    
    if ($params) {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->query($sql);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode($rooms ?: []);



} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
