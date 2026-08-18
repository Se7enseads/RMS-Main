# TODOs
 - [ ] Outline in the scope that it does not cover kibandas
 - [ ] Define Small and Medium Enterprises

## Feature List TODOs

1. [ ] Add Admin close day button
    - [x] Role Creation should include a selection for available permissions
    - [ ] Variance display
        - [ ] After daily stock-take a variance is performed to
2. [ ] Store display
    - [x] Insert the individual ingredients to the store
    - [x] Store admin page.
        - [ ] Variance display
2. [x] Add Kitchen Display
    - [x] food should be displayed here
3. [x] Add Bar Display
    - [x] Drinks should go to the bar display
3. [ ] Add Cashier Display
    - [ ] Add Payment Display and receipt printing (integrate Daraja API)
4. [ ] Add Reports in Admin
    - Use Tabulator for table printing and download/export functions.
5. [ ] Add Kiosk:
    - [ ] Add bill printing
    - [ ] Voiding of orders by (Owner, Manager if right is given)
    - [ ] Settings Page
    - [ ] Payment display for the day
    - [ ] Automatic log out (session timeout)
    - [ ] Dashboard UI polish (from inline TODOs in `src/Views/kiosk/dashboard.php`):
        - [ ] Stacked sections instead of side-by-side; remove open-orders duplication (invisible table)
        - [ ] Fix dashboard-header alignment
        - [ ] "Today" link → button style
        - [ ] Order card style + status button style
        - [ ] Currency icon from DB
        - [ ] Pluralize "items" when count > 1
        - [ ] Show eating status (dine-in vs takeaway)

- [ ] Transactions on all DML (insert, update, delete)
    - [ ] add CSRF tokens
- [ ] Proper error handling Frontend and Backend
    - [ ] Dismissable errors
    - [ ] Timeout frontend errors
    - [ ] delete and reset state, if session is wrong

- [x] Testing
    - [x] Unit tests
    - [x] HTTP tests

## CHECKLIST

Check if all routes:

- [ ] have proper auth
- [ ] have proper CSRF protection
    - [ ] delete and reset state, if session is wrong
- [ ] have necessary permissions
    - [ ] delete and reset state, if session is wrong
- [ ] use semantic HTML
  - [ ] details for accordion
  - [ ] dialog for modals
- [ ] make sure titles match up
