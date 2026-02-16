<?php include 'dbconnect.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { cursor: pointer; background: #f4f4f4; }
        #searchBox { margin-bottom: 15px; padding: 8px; width: 250px; }
    </style>
</head>
<body>

<h2>Sales Report</h2>

<input type="text" id="searchBox" placeholder="Search sales..." onkeyup="searchTable()">

<table id="salesTable">
    <thead>
        <tr>
            <th onclick="sortTable(0)">Product</th>
            <th onclick="sortTable(1)">Quantity</th>
            <th onclick="sortTable(2)">Price</th>
            <th onclick="sortTable(3)">Total</th>
            <th onclick="sortTable(4)">Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT product_name, quantity, price, date FROM sales ORDER BY date DESC";
        $result = $conn->query($sql);

        $monthlyTotal = 0;
        $yearlyTotal = 0;

        $currentMonth = date('m');
        $currentYear = date('Y');

        while ($row = $result->fetch_assoc()) {
            $total = $row['quantity'] * $row['price'];

            // Monthly total
            if (date('m', strtotime($row['date'])) == $currentMonth) {
                $monthlyTotal += $total;
            }

            // Yearly total
            if (date('Y', strtotime($row['date'])) == $currentYear) {
                $yearlyTotal += $total;
            }

            echo "<tr>
                    <td>{$row['product_name']}</td>
                    <td>{$row['quantity']}</td>
                    <td>\${$row['price']}</td>
                    <td>\$" . number_format($total, 2) . "</td>
                    <td>{$row['date']}</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<h3>Monthly Sales Total: $<?php echo number_format($monthlyTotal, 2); ?></h3>
<h3>Yearly Sales Total: $<?php echo number_format($yearlyTotal, 2); ?></h3>

<script>
// FUNCTION
function searchTable() {
    let input = document.getElementById("searchBox").value.toLowerCase();
    let rows = document.querySelectorAll("#salesTable tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}

// FUNCTION
function sortTable(colIndex) {
    let table = document.getElementById("salesTable");
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
