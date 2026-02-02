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
- **Manual Testing:** Manual testing was conducted to verify layout consistency, screen flow, and usability from a cashier’s perspective.


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


