<?php
date_default_timezone_set("Asia/Manila");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json");

$conn = new mysqli("127.0.0.1", "root", "", "database_cotidiano", 3306);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$conn->query("ALTER TABLE `order` ADD COLUMN IF NOT EXISTS isArchived TINYINT(1) NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE `order` ADD COLUMN IF NOT EXISTS archivedAt DATETIME NULL");
$conn->query("CREATE TABLE IF NOT EXISTS sales_transaction (
    saleID INT NOT NULL AUTO_INCREMENT,
    orderID INT NOT NULL,
    saleAmount DECIMAL(10,2) NOT NULL,
    recordedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (saleID),
    UNIQUE KEY orderID (orderID),
    KEY idx_recorded_at (recordedAt),
    CONSTRAINT sales_transaction_order_fk FOREIGN KEY (orderID) REFERENCES `order` (orderID) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$conn->query("INSERT IGNORE INTO sales_transaction (orderID, saleAmount)
    SELECT orderID, totalAmount FROM `order` WHERE orderStatus = 'Delivered'");

$deliveredResult = $conn->query("SELECT COUNT(*) AS deliveredCount FROM `order` WHERE orderStatus='Delivered' AND isArchived=0");
$deliveredCount = intval($deliveredResult->fetch_assoc()["deliveredCount"] ?? 0);
$archiveCount = max(0, $deliveredCount - 50);
if ($archiveCount > 0) {
    $oldestResult = $conn->query("SELECT orderID FROM `order` WHERE orderStatus='Delivered' AND isArchived=0 ORDER BY orderDate ASC, orderID ASC LIMIT $archiveCount");
    while ($row = $oldestResult->fetch_assoc()) {
        $archiveID = intval($row["orderID"]);
        $conn->query("UPDATE `order` SET isArchived=1, archivedAt=NOW() WHERE orderID=$archiveID");
    }
}
?>