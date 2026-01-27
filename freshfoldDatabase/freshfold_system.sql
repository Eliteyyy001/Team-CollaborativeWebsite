DROP DATABASE IF EXISTS freshfold_system;
CREATE DATABASE freshfold_system;
USE freshfold_system;


CREATE TABLE Role(
    roleID          INT             NOT NULL    AUTO_INCREMENT,
    roleName        VARCHAR(50)     NOT NULL,
    PRIMARY KEY (roleID)
);


CREATE TABLE User(
    userID          INT             NOT NULL    AUTO_INCREMENT,
    userName        VARCHAR(100)    NOT NULL,
    userPasscode    VARCHAR(100)    NOT NULL,
    userEmail       VARCHAR(100)    NOT NULL,
    roleID          INT             NOT NULL,
    activityStatus  BOOLEAN         NOT NULL DEFAULT TRUE,
    PRIMARY KEY (userID),
    FOREIGN KEY (roleID) REFERENCES Role(roleID)
);


CREATE TABLE Customer(
    custID          INT             NOT NULL    AUTO_INCREMENT,
    custName        VARCHAR(255)    NOT NULL,
    custEmail       VARCHAR(200)    UNIQUE,
    custPasscode    VARCHAR(100)    UNIQUE,
    custAddress     VARCHAR(255),
    custActiveStatus BOOLEAN        DEFAULT TRUE,
    PRIMARY KEY (custID)
);


CREATE TABLE Supplier(
    supID           INT             NOT NULL    AUTO_INCREMENT,
    supName         VARCHAR(100)    NOT NULL,
    supEmail        VARCHAR(100)    UNIQUE,
    supPhone        VARCHAR(25),
    supAddress      VARCHAR(255),
    supZip          VARCHAR(20),
    supActiveStatus BOOLEAN         DEFAULT TRUE,
    PRIMARY KEY (supID)
);


CREATE TABLE Category(
    catID           INT             NOT NULL    AUTO_INCREMENT,
    catName         VARCHAR(100)    NOT NULL,
    PRIMARY KEY (catID)
);


CREATE TABLE Product(
    prodID              INT             NOT NULL    AUTO_INCREMENT,
    prodName            VARCHAR(100)    NOT NULL,
    catID               INT             NOT NULL,
    prodCost            DECIMAL(10,2),
    quantityStocked     INT             DEFAULT 0,
    prodDiscount        DECIMAL(10,2),
    PRIMARY KEY (prodID),
    FOREIGN KEY (catID) REFERENCES Category(catID)
);


CREATE TABLE ProductThreshold(
    prodID          INT         NOT NULL,
    targetLevel     INT         NOT NULL,
    reorderPoint    INT         NOT NULL,
    PRIMARY KEY (prodID),
    FOREIGN KEY (prodID) REFERENCES Product(prodID)
);


CREATE TABLE LowStockAlert(
    alertID         INT         NOT NULL    AUTO_INCREMENT,
    prodID          INT         NOT NULL,
    quantityOnHand  INT         NOT NULL,
    resolveStatus   BOOLEAN     NOT NULL,
    PRIMARY KEY (alertID),
    FOREIGN KEY (prodID) REFERENCES Product(prodID)
);


CREATE TABLE Service(
    servID          INT             NOT NULL    AUTO_INCREMENT,
    servName        VARCHAR(100),
    servPrice       DECIMAL(10,2),
    taxRate         DECIMAL(5,2),
    servOffering    BOOLEAN         DEFAULT TRUE,
    servDiscount    DECIMAL(10,2),
    PRIMARY KEY (servID)
);


CREATE TABLE Sale(
    saleID          INT             NOT NULL    AUTO_INCREMENT,
    userID          INT             NOT NULL,
    saleDateTime    DATETIME        NOT NULL,
    totalAmount     DECIMAL(10,2)   NOT NULL,
    PRIMARY KEY (saleID),
    FOREIGN KEY (userID) REFERENCES User(userID)
);


CREATE TABLE SaleItem(
    saleItemID      INT             NOT NULL    AUTO_INCREMENT,
    saleID          INT             NOT NULL,
    prodID          INT             NOT NULL,
    quantity        INT             NOT NULL,
    itemPrice       DECIMAL(10,2)   NOT NULL,
    PRIMARY KEY (saleItemID),
    FOREIGN KEY (saleID) REFERENCES Sale(saleID),
    FOREIGN KEY (prodID) REFERENCES Product(prodID)
);


CREATE TABLE InventoryMovement(
    movementID          INT             NOT NULL    AUTO_INCREMENT,
    prodID              INT             NOT NULL,
    transType           ENUM('Sale','Return') NOT NULL,
    transID             INT             NOT NULL,
    quantityChange      INT             NOT NULL,
    unitCost            DECIMAL(10,2),
    movedAt             DATETIME        NOT NULL,
    movedBy             INT             NOT NULL,
    prodActivityStatus  BOOLEAN         NOT NULL,
    PRIMARY KEY (movementID),
    FOREIGN KEY (prodID) REFERENCES Product(prodID),
    FOREIGN KEY (movedBy) REFERENCES User(userID)
);


CREATE TABLE AuditLog(
    auditID         INT             NOT NULL    AUTO_INCREMENT,
    userID          INT             NOT NULL,
    actionType      VARCHAR(100)    NOT NULL,
    affectedEntity  VARCHAR(100),
    actionTime      DATETIME        NOT NULL,
    PRIMARY KEY (auditID),
    FOREIGN KEY (userID) REFERENCES User(userID)
);

-- test data insertion

INSERT INTO Role (roleName) VALUES
('Administrator'),
('Manager'), 
('Cashier'), 
('Owner'),
('Customer');

INSERT INTO User (userName, userPasscode, roleID, activityStatus, userEmail) VALUES
('freshfold_admin1', 'admin321', 1, TRUE, 'admin1@freshfold.com'),
('main_manager', 'manage123', 2, TRUE, 'management@freshfold.com'),
('freshfold_admin2', 'notadmin', 1, FALSE, 'admin2@freshfold.com'),
('cashier1', 'cashout123', 3, TRUE, 'checkout@freshfold.com'),
('fresh_owner', 'fresh321', 4, TRUE, 'ownership@freshfold.com'),
('manager2', 'manage456', 2, TRUE, 'management2@freshfold.com'),
('John Brown', 'fold123', 5, TRUE, 'johnbrown@gmail.com');

INSERT INTO Customer (custName, custEmail, custPasscode, custAddress, custActiveStatus) VALUES
('Jane Doe', 'janedoe@icloud.com', 'doe123', '1214 Grand Ave', TRUE),
('Anne Smith', 'annsmith@gmail.com', 'smithpasswrd', '1417 Arnold St', TRUE),
('Johnathon Lee', 'leejohn@gmail.com', 'notapassword', '1609 Main St', FALSE);

INSERT INTO Supplier (supName, supEmail, supPhone, supAddress, supZip, supActiveStatus) VALUES
('Detergent World', 'detergentworld@contact.com', '301-223-6098', '1304 Sunrise Dr.', '14706', TRUE),
('CleanFreaks', 'cleanfreaks@support.com', '917-789-0086', '312 Business Ave.', '78405', TRUE),
('Laundrobuddy', 'laundrobuddy@support.com', '615-777-0965', '211 Allen St.', '56901', TRUE);

INSERT INTO Category (catName) VALUES
('detergent'),
('dryer supplies'),
('folding'),
('fabric softener'),
('bleaches'),
('stain remover'),
('laundry bags');

INSERT INTO Product (catID, prodName, prodCost, quantityStocked, prodDiscount) VALUES
(3,'FlipFold', 12.99, 80, 2.00),
(3,'Box Legend Folding Board', 11.99, 95, 1.00),
(2,'Wool Dryer Balls', 7.99, 150, 0.50),
(2,'Tide Dryer Sheets', 4.99, 300, 0.75),
(1,'Tide Stain Fighting Detergent', 6.99, 100, 0.00),
(4, 'Downey Fabric Softner', 7.99, 200, 0.25),
(1, 'Downey Sensitive Detergent', 8.99, 120, 2.00);

INSERT INTO ProductThreshold (prodID, targetLevel, reorderPoint) VALUES
(1, 100, 20),
(2, 50, 10),
(3, 80, 15),
(4, 30, 5),
(5, 50, 10),
(6, 150, 30),
(7, 100, 20);

INSERT INTO LowStockAlert (prodID, quantityOnHand, resolveStatus) VALUES
(4, 3, FALSE),
(2, 8, TRUE);

INSERT INTO Service (servName, servPrice, taxRate, servOffering, servDiscount) VALUES
('Wash & Dry', 15.00, 6.00, TRUE, 0.00),
('Dry Clean - Shirt', 10.00, 5.00, TRUE, 0.50),
('Dry Clean - Pants', 12.00, 4.00, TRUE, 1.00),
('Wash & Dry & Fold', 20.00, 6.00, TRUE, 1.25),
('Bulk Wash & Dry (up to 10lbs)', 22.00, 2.00, TRUE, 2.00),
('Sensitive Wash', 16.00, 5.00, TRUE, 0.00);

INSERT INTO Sale (userID, saleDateTime, totalAmount) VALUES
(2, '2025-08-23 10:25:00', 34.76),
(4, '2026-01-03 9:37:00', 29.99),
(6, '2025-12-14 11:08:00', 31.75),
(5, '2025-07-12 8:30:00', 25.65);

INSERT INTO SaleItem (saleID, prodID, quantity, itemPrice) VALUES
(4, 2, 1, 10.75),
(1, 4, 3, 25.00);

INSERT INTO InventoryMovement (prodID, transType, transID, quantityChange, unitCost, movedAt, movedBy, prodActivityStatus) VALUES
(1, 'Sale', 102, -3, 12.99, '2025-08-23 10:25:00', 2, TRUE),
(6, 'Return', 110, 1, 4.99, '2025-07-12 8:30:00', 3, TRUE),
(7, 'Sale', 208, -2, 8.99, '2025-12-14 11:08:00', 5, TRUE);

INSERT INTO AuditLog (userID, actionType, affectedEntity, actionTime) VALUES
(2, 'LOGIN', 'System', '2024-01-26 09:00:00'),
(2, 'CREATE_SALE', 'Sale 1001', '2024-01-26 09:30:00'),
(3, 'LOGIN', 'System', '2024-01-26 11:00:00'),
(3, 'CREATE_SALE', 'Sale 1002', '2024-01-26 11:15:00'),
(1, 'VIEW_REPORT', 'Daily Sales Report', '2024-01-26 17:15:00');


SELECT 'FreshFold database created successfully!' AS Message;

