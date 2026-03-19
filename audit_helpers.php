<?php
/**
 omits the need to add insert statements to all files using audit logging.
 uses AuditLog table
 */
function audit_log(mysqli $conn, int $performedByUserID, string $actionType, ?string $affectedEntity = null): void
{
    if ($performedByUserID <= 0) {
        return;
    }

    $actionType = trim($actionType);
    if ($actionType === '') {
        return;
    }

    $entity = $affectedEntity !== null ? trim($affectedEntity) : null;
    if ($entity === '') {
        $entity = null;
    }

    $stmt = $conn->prepare("INSERT INTO AuditLog (performedByUserID, actionType, affectedEntity) VALUES (?, ?, ?)");
    if (!$stmt) {
        return;
    }

    $stmt->bind_param("iss", $performedByUserID, $actionType, $entity);
    $stmt->execute();
    $stmt->close();
}

function current_user_id(): int
{
    return isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : 0;
}

function current_user_role_id(): int
{
    return isset($_SESSION['roleID']) ? (int)$_SESSION['roleID'] : 0;
}

function can_override_totals(): bool
{
  
    $roleID = current_user_role_id();
    return in_array($roleID, [1, 2, 4], true);
}
?>
