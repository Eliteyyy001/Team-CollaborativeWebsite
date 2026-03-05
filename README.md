# FreshFold POS – Team Collaborative Website

## Project Overview
Collaborative sales/POS system built by the team for FreshFold products. Allows users to view products, add to cart, make sales, and handle payments with alerts for low stock.

## Team Members
- Hannah 
- Trev
- Eliteyy
- Hamza

## Features Implemented
- Dashboard and product listing with real-time low-stock banners (colored badges: green/yellow/red)
- Cart system with quantity adjustments and delete
- Payment simulation (credit cards/digital wallets)
- Login & logout functionality
- Override prompt for low/out-of-stock items
- Top warning banner for low stock alerts
- Responsive UI with modals for basket/payment/thank you

## How to Run Locally
1. Clone the repo: `git clone https://github.com/Eliteyy001/Team-ColaborativeWebsite.git`
2. Set up a local PHP server (e.g., XAMPP, WAMP, or `php -S localhost:8000`)
3. Open in browser: http://localhost/freshfold-admin/pos.php (adjust path if needed)
4. No database needed yet (hard-coded products)

## Recent Work / Sprints
- **Sprint 4**: Implemented Alert and Override UI (issue #55) – PR #52
  - Low-stock banners in pos.php table
  - Override confirm prompt on add
  - Top yellow warning banner

## Setup Notes
- Background image: image.png (place in root)
- Future: Add real database connection for products/stock

## Contributors
 Hannah, Trev, Eliteyy, Hamza.
