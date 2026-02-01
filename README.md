# Team#CollaborativeWebsite

Our group aims to leverage each member’s individual skills and knowledge to efficiently complete all assignments and achieve top quality results, using our respectful, productive, and  collaborative working environment.

## Project Board

Collaborative issues for all user stories
https://github.com/users/hannahinjety04/projects/1

## New Features

**Freshfold Database**
Freshfold database schema which includes the following tables: Roles, Users, Category, SaleItem, Sale, Customer, Product, Product Threshold, Service, AuditLog, InventoryMovement, Supplier, LowStockAlert
                 
This database contains seed data for each table, used for testing queries, realtions, and application functions. Additionally our database supports role-based access, product categorization, supplier management, inventory monvement tracking, sales/sale item records, service records, automated low stock alerts, and audit logging. 

  
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
