<?php include 'dbconnect.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
    <style>
        .low-stock { color: red; font-weight: bold; }
        .out-stock { color: darkred; font-weight: bold; }
        .in-stock { color: green; font-weight: bold; }
        th { cursor: pointer; }
    </style>
</head>
<body>

<h2>Inventory List</h2>

<input type="text" id="searchBox" placeholder="Search products..." onkeyup="searchTable()">

<table id="inventoryTable" border="1" cellpadding="10">
    <thead>
        <tr>
            <th onclick="sortTable(0)">Product Name</th>
            <th onclick="sortTable(1)">Category</th>
            <th onclick="sortTable(2)">Quantity</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT product_name, category, quantity FROM inventory";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $qty = $row['quantity'];

            if ($qty == 0) {
                $status = "<span class='out-stock'>Out of Stock</span>";
            } elseif ($qty < 10) {
                $status = "<span class='low-stock'>Low Stock</span>";
            } else {
                $status = "<span class='in-stock'>In Stock</span>";
            }

            echo "<tr>
                    <td>{$row['product_name']}</td>
                    <td>{$row['category']}</td>
                    <td>{$row['quantity']}</td>
                    <td>$status</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<script>
// FUNCTION
function searchTable() {
    let input = document.getElementById("searchBox").value.toLowerCase();
    let rows = document.querySelectorAll("#inventoryTable tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}

// FUNCTION
function sortTable(colIndex) {
    let table = document.getElementById("inventoryTable");
    let switching = true;
    let direction = "asc";

    while (switching) {
        switching = false;
        let rows = table.rows;

        for (let i = 1; i < rows.length - 1; i++) {
            let shouldSwitch = false;
            let x = rows[i].getElementsByTagName("TD")[colIndex];
            let y = rows[i + 1].getElementsByTagName("TD")[colIndex];

            if (direction === "asc" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                shouldSwitch = true;
            } else if (direction === "desc" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                shouldSwitch = true;
            }

            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                break;
            }
        }

        if (!switching && direction === "asc") {
            direction = "desc";
            switching = true;
        }
    }
}
</script>

</body>
</html>
