# Sprint Log

---

## Sprint Summary

| Item | Details |
|-----|--------|
| Sprint # | Sprint 1 |
| Sprint Dates | [Add start date] – [Add end date] |
| Team Name | Freshfold |
| Members Present | [List team members present for planning/review] |

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
