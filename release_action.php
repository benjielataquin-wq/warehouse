<?php

include_once "auth_check.php";

include_once "conn.php";

$response = [
    "success" => false,
    "message" => ""
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $releaseId = isset($_POST["release_id"])
        ? (int) $_POST["release_id"]
        : 0;

    $action = isset($_POST["action"])
        ? trim($_POST["action"])
        : "";


    if ($releaseId <= 0) {

        $response["message"] = "Invalid release ID.";

    } elseif ($action === "release") {

        try {

            $stmt = $conn->prepare("
                UPDATE tb_release_requests
                SET status = 'Released'
                WHERE release_id = ?
            ");

            $stmt->execute([$releaseId]);

            if ($stmt->rowCount() > 0) {

                $response["success"] = true;
                $response["message"] = "Release completed.";

            } else {

                $response["message"] = "Release request not found or already released.";

            }

        } catch (PDOException $e) {

            $response["message"] = "Failed to release the request.";

        }

    } elseif ($action === "cancel") {

        try {

            $stmt = $conn->prepare("
                UPDATE tb_release_requests
                SET status = 'Cancelled'
                WHERE release_id = ?
            ");

            $stmt->execute([$releaseId]);

            if ($stmt->rowCount() > 0) {

                $response["success"] = true;
                $response["message"] = "Release request cancelled.";

            } else {

                $response["message"] = "Release request not found or already cancelled.";

            }

        } catch (PDOException $e) {

            $response["message"] = "Failed to cancel the request.";

        }

    } else {

        $response["message"] = "Invalid action.";

    }

}

header("Content-Type: application/json");
echo json_encode($response);

?>