# Team#CollaborativeWebsite

Our group aims to leverage each member’s individual skills and knowledge to efficiently complete all assignments and achieve top quality results, using our respectful, productive, and  collaborative working environment.

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



     
