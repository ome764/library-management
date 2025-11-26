Test Cases Template and Examples

Instructions:
- Each team member should create at least 3 test cases for each team member (or for their own components).
- Use the template below; mark Status as `Passed` or `Failed` by comparing Actual vs Expected.

Template:
- Test Case ID: TC-<member>-<number>
- Title: Short title describing the test
- Precondition: State the state before test (e.g., user logged in)
- Steps: 1) ... 2) ...
- Expected Result: What should happen
- Actual Result: What happened during testing
- Status: Passed / Failed
- Notes: Additional comments


Examples (replace Member1/2/3 with real names):

---
Member1 - Example Test Cases

- Test Case ID: TC-Member1-01
- Title: Register new member (valid data)
- Precondition: None
- Steps:
  1) Open registration page
  2) Fill name, email, password
  3) Submit
- Expected Result: Registration succeeds and confirmation message shown
- Actual Result: (fill after test)
- Status: (Passed/Failed)
- Notes: For Phase 1 this is simulated; Phase 2 will verify DB insertion.

- Test Case ID: TC-Member1-02
- Title: Search book by title
- Precondition: Catalog page open
- Steps:
  1) Type "Clean Code" in search
- Expected Result: Book "Clean Code" is visible in results
- Actual Result: (fill after test)
- Status: (Passed/Failed)

- Test Case ID: TC-Member1-03
- Title: Borrow book (static flow)
- Precondition: Book details page open
- Steps:
  1) Enter valid Member ID
  2) Click Borrow
- Expected Result: Alert shows borrow simulated message
- Actual Result: (fill after test)
- Status: (Passed/Failed)

---
Member2 - Example Test Cases

- Test Case ID: TC-Member2-01
- Title: Login with incorrect password
- Precondition: Login page open
- Steps:
  1) Enter registered email
  2) Enter wrong password
  3) Submit
- Expected Result: Error message about invalid credentials
- Actual Result: (fill after test)
- Status: (Passed/Failed)

- Test Case ID: TC-Member2-02
- Title: View book details page
- Precondition: Catalog page open
- Steps:
  1) Click View on a book
- Expected Result: Book details displayed with borrow form
- Actual Result: (fill after test)
- Status: (Passed/Failed)

- Test Case ID: TC-Member2-03
- Title: Responsive layout check
- Precondition: Browser resized to mobile width
- Steps:
  1) Open catalog page
- Expected Result: Layout adapts, content readable
- Actual Result: (fill after test)
- Status: (Passed/Failed)

---
Member3 - Example Test Cases

- Test Case ID: TC-Member3-01
- Title: Reservation attempt (static)
- Precondition: Book is marked unavailable (simulate)
- Steps:
  1) Click Reserve (Phase 1: simulate)
- Expected Result: Reservation confirmation (simulated)
- Actual Result: (fill after test)
- Status: (Passed/Failed)

- Test Case ID: TC-Member3-02
- Title: Fine calculation logic (unit example)
- Precondition: Borrowed book overdue by 3 days
- Steps:
  1) Run fine calculation routine (Phase 2 unit)
- Expected Result: Fine = daily_rate * days_overdue
- Actual Result: (fill after test)
- Status: (Passed/Failed)

- Test Case ID: TC-Member3-03
- Title: Admin panel access (authorization)
- Precondition: User logged in as non-admin
- Steps:
  1) Attempt to access admin URL
- Expected Result: Access denied / redirect
- Actual Result: (fill after test)
- Status: (Passed/Failed)


Notes:
- For Phase 1, many flows are simulated (no DB writes). Phase 2 will convert these into real integration tests against the MySQL database and PHP endpoints.
- Replace placeholder names and add more test cases as the project develops.
