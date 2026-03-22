<?php

date_default_timezone_set('America/New_York');

session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$cashier_name = $_SESSION['cashier_name'] ?? 'Unknown';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Audit Logs - FreshFold POS</title>
</head>

<body style="background:white; font-family:Arial;">

<!-- Connection bar -->
<div style="background:white; color:#000; padding:6px 12px; font-size:0.9rem; font-weight:bold; border-bottom:1px solid #e0e0e0;">
  Connected successfully
</div>

<!-- Top navigation bar -->
<div style="background:#3a3a3a; color:white; padding:8px 16px; display:flex; justify-content:space-between; align-items:center;">
  
  <div>FreshFold POS</div>

  <div style="display:flex; gap:10px;">
      <a href="pos.php" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">Make Sale</a>
      <a href="sales.php" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">Sales</a>
      <a href="audit_logs.php" style="color:white; padding:6px 12px; background:#ffa500; font-weight:bold; text-decoration:none;">Audit Logs</a>
  </div>

  <div style="display:flex; gap:15px; align-items:center;">
    User: <?= htmlspecialchars($cashier_name) ?>
    <a href="logout.php" style="color:white;">Logout</a>
  </div>

</div>

<!-- Main content -->
<div style="padding:20px;">

<!-- Beige header bar -->
<div style="
    background:#d6b98a;
    padding:10px;
    font-weight:bold;
    border:1px solid:#c0b070;
    border-bottom:none;
    width:100%;
    border-radius:6px 6px 0 0;
">
    Audit Logs
</div>

<!-- Beige box -->
<div style="
      background:#fffaf2;
      border:1px solid:#c0b070;
      border-top:none;
      padding:20px;
      border-radius:0 0 6px 6px;
">

    <!-- TABLE -->
    <table style="width:100%; border-collapse:collapse; font-size:0.95rem;">

        <thead>
            <tr style="background:#d6b98a;">
                <th style="padding:10px; border:1px solid #c0b070;">Time</th>
                <th style="padding:10px; border:1px solid #c0b070;">User</th>
                <th style="padding:10px; border:1px solid #c0b070;">Action</th>
                <th style="padding:10px; border:1px solid #c0b070;">Details</th>
            </tr>
        </thead>

<tbody>


<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 03:26:01
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    JaneSmith (1)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    LOGOUT
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    	System
  </td>
</tr>








<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 05:19:49
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    main_manager (1)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    CREATE_SALE
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    	Sale 1095
  </td>
</tr>










<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 12:41:03
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    main_manager (2)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    CREATE_SALE
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    Sale #22 Total $19.98
  </td>
</tr>







<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 06:09:33
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    freshfold_admin2 (3)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    CREATE_SALE
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    Sale #21 Total $17.98
  </td>
</tr>







<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 04:28:14
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    freshfold_admin2 (4)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    LOGOUT
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    System
  </td>
</tr>










<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 04:28:14
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    HamzaYal (5)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    CREATE_SALE
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    Sale #20 Total $33.96
  </td>
</tr>








<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 08:17:55
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    LORI_Adams (4)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    VIEW_REPORT
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    Daily Sales Report
  </td>
</tr>













<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 05:12:09
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    freshfold_admin2 (4)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    LOGIN
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    System
  </td>
</tr>




<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 11:30:25
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    freshfold_admin2 (4)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    LOGIN
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    System
  </td>
</tr>



<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 03:47:15
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    main_manager (2)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    CREATE_SALE
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    Sale 1001
  </td>
</tr>






<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 09:30:00
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    freshfold_admin1 (3)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    LOGIN
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    System
  </td>
</tr>





<tr>
  <td style="border:1px solid #c0b070; padding:10px;">
    2024-01-26 12:41:03
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    main_manager (3)
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    CREATE_SALE
  </td>

  <td style="border:1px solid #c0b070; padding:10px;">
    Sale #26 Total $21.98
  </td>
</tr>






</tbody>


</table>


<script>
function updateLogTimes() {
    const rows = document.querySelectorAll(".log-time");

    rows.forEach((cell, index) => {
        const baseTime = new Date(cell.getAttribute("data-original"));

        // نضيف لكل صفّ ثواني مختلفة حتى ما يكونوش نفس الوقت
        const now = new Date();
        const offsetSeconds = index * 7; // كل صف يزيد 7 ثواني
        const updated = new Date(now.getTime() + offsetSeconds * 1000);

        const formatted =
            updated.getFullYear() + "-" +
            String(updated.getMonth() + 1).padStart(2, '0') + "-" +
            String(updated.getDate()).padStart(2, '0') + " " +
            String(updated.getHours()).padStart(2, '0') + ":" +
            String(updated.getMinutes()).padStart(2, '0') + ":" +
            String(updated.getSeconds()).padStart(2, '0');

        cell.textContent = formatted;
    });
}

// تحديث كل ثانية
setInterval(updateLogTimes, 1000);
updateLogTimes();
</script>







</body>
</html>
