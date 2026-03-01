# Team#CollaborativeWebsite

Our group aims to leverage each member's individual skills and knowledge to efficiently complete all assignments and achieve top quality results, using our respectful, productive, and  collaborative working environment.

## Project Board

Collaborative issues for all user stories
https://github.com/users/hannahinjety04/projects/1

## New Features

**Freshfold Database**
Freshfold database schema which includes the following tables: Roles, Users, Category, SaleItem, Sale, Customer, Product, Product Threshold, Service, AuditLog, InventoryMovement, Supplier, LowStockAlert
                 
This database contains seed data for each table, used for testing queries, realtions, and application functions. Additionally our database supports role-based access, product categorization, supplier management, inventory monvement tracking, sales/sale item records, service records, automated low stock alerts, and audit logging. 

**Authentication**
- Users can sign up and log in using a username and password
- Passwords are securely hashed before being stored in the database
- Login credentials are validated against stored user records
- Authentication is handled entirely on the backend
- full access to administrative functionality with admin login
– restricted access to employee-level functionality

**Authorization (Roles)**
Role-based access is enforced using the `Users` and `Roles` tables in the database.

 **POS UI Design**

This feature provides the user interface design for the Point of Sale (POS) system used by cashiers. The POS layout is designed to be clean, intuitive, and efficient, supporting a smooth checkout workflow.

The POS interface includes product listings, a cart area to track selected items, a checkout button, and a navigation layout that supports logical screen flow.


**Dashboard & Reporting Framework**

This feature provides the foundational user interface for the Freshfold dashboard and reporting system. It offers a structured layout for displaying key metrics, summaries, and reports that will support decision-making and system monitoring.

The dashboard and reporting pages are currently built using placeholder data to establish layout, navigation, and visual structure. These pages are designed to be easily integrated with real backend data in future sprints.

**Cart Page**

This feature provides the ability to add items to the cart through the "Make Sale" page.  Users can update the item quanitity inside of the cart.  Items can be removed and users have the ability to navigate back to the "Make Sale" page to update the cart items.  Price is displayed. Frontend validation includes ensuring that item quantity is greater than 0. Additionally, cart will display a warning when clicking the checkout button with an empty cart.
_Note: Cannot checkout yet, as this logic has not been set up yet._

**Admin User Management**

This feature provides admin-only access to manage system users. Admins can log in through a dedicated admin login page and access a dashboard to view, add, edit, and deactivate users. Role-based access is enforced through session validation to ensure only users with the Administrator role can access admin functionality.

**Inventory UI**

This feature provides a user-friendly interface for viewing and managing the inventory in the Freshfold system. It includes a sortable inventory list, search functionality, and visual indicators for low-stock items. Displays all products in the inventory with details such as product name, category, quantity, supplier, and stock status; allows users can sort inventory by columns, search for specific products using a search bar that filters results in real time, and give low stock indications when product quantities are below threshold. 

**Inventory Backend**
This feature provides the backend logic for adding prodcuts, creating a way for admins to view products and stock levels, the ability to edit product details, adjust stock quantities, delete inactive products, and create inventory updates when admins make changes to products. This logic also blocks negative stock. 

**Sales Backend**
This feature provides the complete backend logic for processing and storing completed sales transactions. It connects the POS checkout process directly to the database and ensures inventory updates occur in real time.

The Sales Backend includes:

- Creation of `Sale` records when a cashier completes checkout
  - Stores `saleDateTime`, `totalAmount`, and `userID`
- Automatic creation of `SaleItem` records at the same time as the sale
  - Stores `prodID`, `quantity`, `itemPrice`, and `inventoryMovement`
- Real-time inventory deduction when a sale is completed
- Logging of inventory movement tied directly to each sale
- Ability to view a list of completed sales
- Ability to view total transaction amounts
- Ability to view line items inside a sale (product, quantity, and price)
- Prevention of inconsistent inventory data through immediate database updates

  ## Usage Instructions

  **Freshfold Database**
  Once the database has been successfully imported, it can be used for testing and system development.
  - Query the 'Users' and 'Roles" tables to test role-based access.
  - Add seed data to tables.
  - Use the 'Product', 'InventoryMovement', and 'ProductThreshold' tables to test inventory tracking
  - Simulate a sale by querying the 'Sale' and 'SaleItem' table
  - Review system activity and changes to the system through the 'AuditLog' table.
  - Use the created seed data to test foreign and primary key relationships and reporting queries.

  This database is intended to be used as a backend data layer for the Freshfold application, or in other words, the backbone of this system.

   **Authentication & Role-Based Access**
- Users can sign up using a username and password:
     - Username: `admin`
     - Password: `admin123`
     - Username: `employee`
     - Password: `employee123`
- Users can log in with valid credentials.
- Passwords are validated against hashed values stored in the database.
- Admin users are granted access to admin-only features.
- Employee users are restricted from accessing admin-only functionality.
- Unauthorized users are prevented from accessing protected routes or pages.

  **POS UI Design**
- View available products in the product list section.
- Add items to the cart area.
- Review selected items before checkout.
- Use the checkout button to proceed with the sale.
- Navigate between POS screens using the provided navigation layout.

**Dashboard & Reporting Framework**
- View dashboard metrics displayed using placeholder data.
- Navigate between dashboard and reporting pages using application navigation.
- Review reporting sections designed to display sales, inventory, and performance data.
- Use these pages as a visual framework for future backend data integration.

**Cart Page**
- Add products via the product panel in "Make Sale" page.
- View cart via link provided near user's name.
- View cart items within the cart page along with their quanitity, price, and total price.
- Click "Remove" to delete any unwanted cart items.
- Click "Update Cart" to be directed back to "Make Sale" page and add new items to cart.

**Admin User Management**
- Access admin login page via `admin-login.php`.
- Log in with Administrator credentials (e.g., `freshfold_admin1` / `admin321`).
- View all users in the system with their roles and status.
- Add new users with username, email, password, and role assignment.
- Edit existing user details.
- Deactivate or activate user accounts.
- Non-admin users are redirected to login page.

**Inventory UI**
- Open the inventory page in your browser via localhost/inventory.php.
- Browse the list of all inventory items.
- Click column headers to sort inventory by product name, category, quantity, or supplier.
- Use the search bar to filter items by name or category.
- Low-stock items are highlighted for easy identification.
- Combine sorting and search features to quickly locate specific products.

**Inventory Backend**
- _Cannot be used till this is connected to a php and html file (incomplete)_

**Sales Backend**
- Add products to cart via the POS interface.
- Click the checkout button to complete a sale.
- Upon checkout:
  - A new record is created in the `Sale` table.
  - Corresponding records are created in the `SaleItem` table.
  - Product inventory is automatically deducted.
  - Inventory movement is logged.
- View completed sales in the sales.php page.
- Select a sale to view its line items including product, quantity, and price in sale_detail.php page.


  ## Setup Steps

**Freshfold Database**
  ###Prerequisites
  - XAMPP installed
  - Apache and MySQL services running
  - Access to phpMyAdmin
  - Command-line shell (XAMPP shell or system terminal)

  ### Steps
  1. Start XAMPP and ensure that **Apache** and **MySQL** are running (both services should be green).
  2. Open phpMyAdmin on browser via the following address: http://localhost/phpmyadmin
  3. Creat a new database
     -Click **New**
     -Enter a database name (e.g., 'freshfold_db)
     -Click **Create**
  4. Import the database schema
     -Select the newly created database located on the far left.
     -Click the **Import** tab at the top.
     -Choose the provided .sql file (freshfold_system.sql)
     -Click **Go**.
  5. Verify that all tables have been created and that each table contains seed data.
  6. Test via shell or system terminal
    -Open the XAMPP shell or system terminal
    -Log into MySQL using the following command: mysql -u root.
    -Selection the database using the following command: USE freshfold_db;
    -Run test queries such as the following: SELECT * FROM Product;
                                             SELECT * FROM Users;


**Authentication & Role-Based Access**

#### Prerequisites
- XAMPP installed
- Apache and MySQL services running
- Freshfold database imported
- Access to phpMyAdmin
- Command-line shell or browser-based testing environment

#### Steps
1. Ensure the Freshfold database is running and contains the `Users` and `Roles` tables.
2. Verify that user roles (Admin, Employee) exist in the `Roles` table.
3. Insert or register test users with assigned roles.
4. Test login functionality using valid and invalid credentials.
5. Verify that role-based restrictions correctly grant or deny access.


**POS UI Design**
#### Prerequisites
- Web browser (Chrome, Firefox, etc.)
- XAMPP running Apache server
- Access to the project files

#### Steps
1. Start XAMPP and ensure **Apache** and **MySQL** is running.
2. Place POS UI files in the appropriate project directory.
3. Open the POS interface in a browser via `localhost`.
4. Verify that all POS screens render correctly.
5. Test navigation flow between screens.
6. Confirm that layout is clean and usable at standard desktop resolution.

**Dashboard & Reporting Framework**

#### Prerequisites
- XAMPP installed
- Apache service running
- Web browser (Chrome, Firefox, etc.)
- Access to the project files

#### Steps
1. Start XAMPP and ensure **Apache** and **MySQL** is running.
2. Place the dashboard and reporting files in the project directory.
3. Open the dashboard page in a browser via `localhost`.
4. Navigate to the reporting page using the provided links.
5. Verify that pages load correctly and display placeholder content.
6. Confirm that navigation works without errors.

**Cart Page**
1. Start XAMPP and ensure that **Apache** and **MySQL** are running.
2. Open the cart page via localhost connection and ensure that it is successfully connected.
3. Add desired products from product table.
4. Click "View Cart" at the top located in the navigation bar.

**Admin User Management**

#### Prerequisites
- XAMPP installed
- Apache and MySQL services running
- Freshfold database imported with Users and Roles tables
- Web browser (Chrome, Firefox, etc.)

#### Steps
1. Start XAMPP and ensure **Apache** and **MySQL** are running.
2. Place admin files (`admin-login.php`, `admin-dashboard.php`, `admin-styles.css`, `admin-logout.php`) in the project directory.
3. Open the admin login page in a browser via `localhost/admin-login.php`.
4. Log in with Administrator credentials.
5. Verify access to admin dashboard with user list.
6. Test add, edit, and deactivate user functions.
7. Verify non-admin users cannot access admin pages.

**Inventory UI**

####Prerequisities 
- XAMPP installed
- Apache and MySQL services running
- Freshfold database imported with Users and Roles tables
- Web browser (Chrome, Firefox, etc.)

#### Steps
1. Start XAMPP and ensure Apache and MySQL are running.
2. Open the inventory page in a browser via http://localhost/inventory.php.
3. Confirm that the inventory table loads all products from the database.
4. Test table sorting by clicking column headers.
5. Test the search bar by typing product names or categories and confirming results are filtered.
6. Verify low-stock indicators are displayed for products with quantity below the threshold in the ProductThreshold table.

**Sales Backend**
#### Prerequisites
- XAMPP installed
- Apache and MySQL services running
- Freshfold database imported
- `Sale`, `SaleItem`, and `Product` tables properly configured
- POS and checkout files connected to the database

#### Steps
1. Start XAMPP and ensure **Apache** and **MySQL** are running.
2. Confirm database connection in `config.php` or database connection file.
3. Open the POS system in your browser via `localhost`.
4. Add products to the cart.
5. Click checkout to complete a sale.
6. Verify:
   - A new entry appears in the `Sale` table.
   - Related entries appear in the `SaleItem` table.


**Inventory Backend**
_Cannot be set up for usage till this is connected to a php and html file_
