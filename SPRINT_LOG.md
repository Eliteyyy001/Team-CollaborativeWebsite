# Sprint Log

---

## Sprint Summary

| Item | Details |
|-----|--------|
| Sprint # | Sprint 1 |
| Sprint Dates | January 20,2026 – February 1,2026 |
| Team Name | SystemSync |
| Members Present | Godspower Ogide, Hamza Yalouli, Hannah Injety, Trevor Lovet |

---

## Tasks vs. Reality

### Planned Tasks
 **Dashboard and Reporting Framework**
- Build the Dashboard UI with placeholder data
- Build the Reporting UI with placeholder data
- Create a dedicated feature branch for the work
- Submit a pull request for review into `develop`

  **Freshfold Database**
- Translate ERD to MySQL tables (create all tables and define PKs, FKs, and constraints)
- Connect database to a server (XAMPP)
- Create seed data for testing in database

  **Authentication Model**
- Implement user signup with form validation
- Implement user login with session handling
- Hash passwords before storing in the database
- Set up role-based access control (admin vs employee)
- Create feature branch for authentication work
- Submit pull request into develop branch

  **POS UI Design**
- Design POS wireframe
- Build POS screens for cashiers
- Build POS navigation layout

### Completed Tasks
**Dashboard and Reporting Framework**
- Added `dashboard.html` with basic layout and placeholder metrics
- Added `reporting.html` with placeholder reporting sections
- Created branch: `feature/dashboard-ui`
- Opened pull request into `develop`
- Verified no merge conflicts

  **Freshfold Database**
- Successfully converted ERD into a working MySQL database schema
- Defined all primary keys, foreign keys, constraints, and data types
- Created INSERT statements for seed/test data
- Connected and tested database using XAMPP and phpMyAdmin

   **Authentication Model**
-Signup and login pages added (signup.html, login.html)
-Password hashing implemented using bcrypt in backend
-Session management set up to keep users logged in
-Role-based access control implemented for admin vs employee
-Created branch: feature/authentication
-Opened pull request into develop
-Verified no merge conflicts

  **POS UI Design** 
- Designed POS wireframe to define layout and cashier workflow
- Built POS screens with clearly structured product list
- Implemented checkout button placement following POS flow
- Created navigation layout
- Verified POS screens render cleanly and consistently

### Incomplete Tasks

**Dashboard and Reporting Framework**
- None  
_All planned tasks for Sprint 1 were completed._

**Freshfold Database**
-None
_All planned tasks for Sprint 1 were completed._

**Authentication Model**
-None
_All planned tasks for Sprint 1 were completed._

**POS UI Design**
-None
_All planned tasks for Sprint 1 were completed._

### Test Report

**Freshfold Database**
- **Unit Testing:** Limited unit testing was condcuted by using HTML pages and SQL scripts. Individual parts such as page loading and database table creation were validated manually.
- **Integration Testing:** Integration testing was conducted to confirm that the database could be imported and accessed successfully using XAMPP and phpMyAdmin.
- **Manual Testing:** Manual browser testing was used to verify UI behavior, navigation, and error-free loading.
  
**Authentication Model**
- **Unit Testing:** Unit-level testing was performed on authentication-related functions such as password hashing and credential validation to ensure correct behavior for valid and invalid inputs.
- **Integration Testing:** Tested login and signup flow end-to-end with MySQL database
- **Manual Testing:**Manual verification of form validation, password hashing, and role assignment. Individual backend routes tested with Postman for expected responses

**POS UI Design**
- **Unit Testing:** Unit testing was minimal for this sprint, as the work primarily involved UI layout and static screen components. Individual UI components were visually verified for correct rendering.
- **Integration Testing:** Integration testing was performed to ensure that navigation between POS screens functioned correctly and that UI sections interacted as expected.
- **Manual Testing:** Manual testing was conducted to verify layout consistency, screen flow, and usability from a cashier's perspective.


### Manual & Integration Testing

| Test Case | Result |
|---------|--------|
| Dashboard loads correctly in browser | Passed |
| Reporting page displays placeholder content | Passed |
| Navigation links work correctly | Passed |
| No console errors during page load | Passed |
| Database imports successfully into MySQL | Passed |
| Tables contain seed data | Passed |
| User can sign up with username and password | Manual | Passed |
| Passwords are stored in hashed format | Unit | Passed |
| User can log in with valid credentials | Manual | Passed |
| Login fails with incorrect password | Unit | Passed |
| Admin role grants access to admin-only features | Integration | Passed |
| Employee role restricts access to admin-only features | Integration | Passed |
| Unauthorized users are prevented from accessing protected routes | Integration | Passed |
| Session persists after successful login | Integration | Passed |
| POS wireframe renders correctly | Manual | Passed |
| Product list displays correctly on POS screen | Manual | Passed |
| Checkout button is visible and accessible | Manual | Passed |
| POS screens render correctly on standard desktop resolution | Manual | Passed |
| Overall POS flow is logical and easy to follow | Manual | Passed |


---

## Bug Tracking

### High-Severity Bugs
- No high-severity bugs were found during Sprint 1.

### Issues Identified
- No UI conflicts with existing pages.
- No database constraint or import errors encountered.
- Minor CSS styling needed on login/signup forms

---

## Notes
- Dashboard and reporting pages currently use placeholder data.
- Database schema and seed data are complete and ready for application integration.
- Real data integration and backend connectivity will be implemented in future sprints.
- Work is ready for peer review and merge into the `develop` branch.
- Authentication is fully functional with role-based access control
- Backend integration with dashboard and reporting pages is ready for next sprint
- Module is ready for peer review and merge into develop branch

---

## Sprint Summary

| Item | Details |
|-----|--------|
| Sprint # | Sprint 2 |
| Sprint Dates | February 2, 2026 – February 15, 2026 |
| Team Name | SystemSync |
| Members Present | Godspower Ogide, Hamza Yalouli, Hannah Injety, Trevor Lovet |

---

## Tasks vs. Reality

### Planned Tasks

**Cart Page**
- Build basic cart structure with "Update Cart" and "Checkout" buttons
- Create ablity to add and remove items to cart
- Create ability to change the quantities of each cart item **inside** of cart
- Frontend validation for quantity to be greater than 0
- Empty cart warnings when clicking the checkout button with no cart items

**Admin User Management**
- Create admin login page with session handling
- Create admin dashboard to display all users
- Create ability for admin to add new users
- Create ability for admin to edit existing users
- Create ability for admin to deactivate/activate users
- Enforce admin-only access through role-based login
- Create feature branch for admin user management work
- Submit pull request into develop branch

### Completed Tasks

**Cart Page**
- Added cart.js, cart.php, and cart.css to develop branch
- Built ability to checkout and update the cart to add more items or purchase items
- Display total price in the cart
- Created the ability for pos.php to add selected products to cart.php
- Created the ability to change the quantity of products within the cart

**Admin User Management**
- Added `admin-login.php` with session handling and credential validation
- Added `admin-dashboard.php` with user list and management forms
- Added `admin-styles.css` matching POS theme
- Added `admin-logout.php` for session logout
- Implemented add user functionality with role assignment
- Implemented edit user functionality
- Implemented activate/deactivate user functionality
- Enforced admin-only access via roleName session check
- Created branch: `feature/admin-user-managemen`
- Opened pull request into `develop`
- Verified no merge conflicts

### Incomplete Tasks

**Cart Page**
- None  
_All planned tasks for Sprint 2 were completed._

**Admin User Management**
- None  
_All planned tasks for Sprint 2 were completed._


## Test Report

### POS Cart Functionality Model

**Unit Testing:**  
Unit testing was conducted on individual cart functions such as adding items, removing items, updating item quantities, validating quantity input, and recalculating cart totals. 

**Integration Testing:**  
Integration testing was performed to ensure the cart.php file functioned correctly with pos.php. This included ensuring that items added from the pos.php page were correctly being added to cart.php or deleted from pos.php and synving with cart.php. Additionally, total price display on pos.php was tested to ensure that the price is being updated within the cart page. 

**Manual Testing:**  
Manual testing was performed by adding multiple items, adjusting quantities, removing items, testing invalid input, and verifying empty cart warnings. Cart responsiveness and good workflow was accurate to what as planned.

### Admin User Management

**Unit Testing:**  
Unit-level testing was performed on form validation, session handling, and database queries for adding, editing, and toggling user status to ensure correct behavior for valid and invalid inputs.

**Integration Testing:**  
Integration testing was performed to ensure admin-login.php correctly validates credentials against the Users table and enforces Administrator role check. Dashboard displays users from database and all CRUD operations update the database correctly.

**Manual Testing:**  
Manual testing was performed by logging in with admin and non-admin accounts, adding new users, editing user details, and deactivating/activating users. Role-based access was verified by confirming non-admin users are redirected to login page.

---

## Manual, Unit & Integration Testing

**Cart Page**

| Test Case | Test Type | Result |
|---------|-----------|--------|
| Cart loads correctly when POS page opens | Manual | Passed |
| User can add item to cart | Integration | Passed |
| Added item displays correct name, price, and quantity | Integration | Passed |
| User can add multiple different items | Integration | Passed |
| User can increase item quantity | Unit | Passed |
| User can decrease item quantity | Unit | Passed |
| Cart total updates correctly when quantity changes | Unit | Passed |
| Cart prevents quantity from being set to 0 | Unit | Passed |
| Cart prevents negative quantity values | Unit | Passed |
| Frontend validation prevents invalid quantity input | Unit | Passed |
| User can remove item from cart | Integration | Passed |
| Cart updates correctly after item removal | Integration | Passed |
| Cart total recalculates correctly after item removal | Unit | Passed |
| Checkout button shows warning when cart is empty | Manual | Passed |
| Empty cart warning message displays correctly | Manual | Passed |
| Cart handles multiple add/remove/update operations correctly | Integration | Passed |
| No console errors occur during cart operations | Manual | Passed |
| Cart UI updates correctly in real time | Manual | Passed |

**Admin User Management**

| Test Case | Test Type | Result |
|---------|-----------|--------|
| Admin login page loads correctly | Manual | Passed |
| Admin can log in with valid credentials | Integration | Passed |
| Login fails with invalid credentials | Unit | Passed |
| Non-admin users are denied access | Integration | Passed |
| Deactivated users cannot log in | Integration | Passed |
| Admin dashboard displays all users | Integration | Passed |
| Admin can add new user | Integration | Passed |
| Duplicate username/email is rejected | Unit | Passed |
| Admin can edit existing user | Integration | Passed |
| Admin can deactivate user | Integration | Passed |
| Admin can activate user | Integration | Passed |
| Admin cannot deactivate own account | Unit | Passed |
| Logout clears session and redirects | Integration | Passed |
| Styling matches POS theme | Manual | Passed |

---

## Bug Tracking

**Cart Page**

### High-Severity Bugs
- No high-severity bugs were found during Sprint 2.

### Issues Identified
- No UI conflicts with existing pages.
- No database constraint or import errors encountered.

**Admin User Management**

### High-Severity Bugs
- No high-severity bugs were found during Sprint 2.

### Issues Identified
- No UI conflicts with existing pages.
- No database constraint or import errors encountered.

---

## Notes
- Cart page is fully functional with quantity validation and empty cart warnings
- Admin user management is fully functional with role-based access control
- Admin dashboard uses existing POS styling for visual consistency
- Work is ready for peer review and merge into the `develop` branch
