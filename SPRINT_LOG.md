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
- Build the Dashboard UI with placeholder data
- Build the Reporting UI with placeholder data
- Create a dedicated feature branch for the work
- Submit a pull request for review into `develop`
- Translate ERD to MySQL tables (create all tables and define PKs, FKs, and constraints)
- Connect database to a server (XAMPP)
- Create seed data for testing in database

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

### Incomplete Tasks
**Dashboard and Reporting Framework**
- None  
_All planned tasks for Sprint 1 were completed._

**Freshfold Database**
-None
_All planned tasks for Sprint 1 were completed._
---

## Test Report

- **Unit Testing:** Limited unit testing was condcuted by using HTML pages and SQL scripts. Individual parts such as page loading and database table creation were validated manually.
- **Integration Testing:** Integration testing was conducted to confirm that the database could be imported and accessed successfully using XAMPP and phpMyAdmin.
- **Manual Testing:** Manual browser testing was used to verify UI behavior, navigation, and error-free loading.

### Manual & Integration Testing

| Test Case | Result |
|---------|--------|
| Dashboard loads correctly in browser | Passed |
| Reporting page displays placeholder content | Passed |
| Navigation links work correctly | Passed |
| No console errors during page load | Passed |
| Database imports successfully into MySQL | Passed |
| Tables contain seed data | Passed |

Testing was performed using browser-based manual testing and MySQL queries via phpMyAdmin and XAMPP shell.

---

## Bug Tracking

### High-Severity Bugs
- No high-severity bugs were found during Sprint 1.

### Issues Identified
- No UI conflicts with existing pages.
- No database constraint or import errors encountered.

---

## Notes
- Dashboard and reporting pages currently use placeholder data.
- Database schema and seed data are complete and ready for application integration.
- Real data integration and backend connectivity will be implemented in future sprints.
- Work is ready for peer review and merge into the `develop` branch.

---

## Authentication Module
Planned Tasks

Implement user signup with form validation

Implement user login with session handling

Hash passwords before storing in the database

Set up role-based access control (admin vs employee)

Create feature branch for authentication work

Submit pull request into develop branch

## Completed Tasks

Signup and login pages added (signup.html, login.html)

Password hashing implemented using bcrypt in backend

Session management set up to keep users logged in

Role-based access control implemented for admin vs employee

Created branch: feature/authentication

Opened pull request into develop

Verified no merge conflicts

Incomplete Tasks

None
All planned tasks for authentication were completed

### Test Report
Unit Testing

Manual verification of form validation, password hashing, and role assignment

Individual backend routes tested with Postman for expected responses

## Integration Testing

### Tested login and signup flow end-to-end with MySQL database

Confirmed role-based redirects (admin pages restricted to admins)

## Manual Testing
Test Case	Result
Signup stores hashed password in database	Passed
Login authenticates valid users	Passed
Login rejects invalid credentials	Passed
Admin-only page access restricted to admins	Passed
Employee page access restricted to employees	Passed
Session persists across page reloads	Passed
Bug Tracking
High-Severity Bugs

None found

Issues Identified

Minor CSS styling needed on login/signup forms

## Notes

Authentication is fully functional with role-based access control

Backend integration with dashboard and reporting pages is ready for next sprint

Module is ready for peer review and merge into develop branch
