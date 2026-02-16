<?php include 'dbconnect.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Profit Margins</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { cursor: pointer; background: #f4f4f4; }
    </style>
</head>
<body>

<h2>Profit Margins Report</h2>

<table id="profitTable">
    <thead>
        <tr>
            <th onclick="sortTable(0)">Product</th>
            <th onclick="sortTable(1)">Revenue</th>
            <th onclick="sortTable(2)">Cost</th>
            <th onclick="sortTable(3)">Profit</th>
            <th onclick="sortTable(4)">Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT product_name, quantity, price, cost, date FROM sales ORDER BY date DESC";
        $result = $conn->query($sql);

        $monthlyProfit = 0;
        $yearlyProfit = 0;

        $currentMonth = date('m');
        $currentYear = date('Y');

        while ($row = $result->fetch_assoc()) {
            $revenue = $row['quantity'] * $row['price'];
            $cost = $row['quantity'] * $row['cost'];
            $profit = $revenue - $cost;

            // Monthly profit
            if (date('m', strtotime($row['date'])) == $currentMonth) {
                $monthlyProfit += $profit;
            }

            // Yearly profit
            if (date('Y', strtotime($row['date'])) == $currentYear) {
                $yearlyProfit += $profit;
            }

            echo "<tr>
                    <td>{$row['product_name']}</td>
                    <td>\$" . number_format($revenue, 2) . "</td>
                    <td>\$" . number_format($cost, 2) . "</td>
                    <td>\$" . number_format($profit, 2) . "</td>
                    <td>{$row['date']}</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<h3>Monthly Profit: $<?php echo number_format($monthlyProfit, 2); ?></h3>
<h3>Yearly Profit: $<?php echo number_format($yearlyProfit, 2); ?></h3>

<script>
// FUNCTION
function sortTable(colIndex) {
    let table = document.getElementById("profitTable");
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
