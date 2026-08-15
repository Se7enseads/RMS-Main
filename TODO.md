# TODOs

## Feature List TODOs

1. [ ] Add Admin close day button
    - [ ] Role Creation should include a selection for available permissions
2. [ ] Add Kitchen Display
3. [ ] Add Cashier Display
    - [ ] Add Payment Display and receipt printing
4. [ ] Add Reports in Admin
    - Use Tabulator for table printing and download/export functions.
5. [ ] Add Kiosk:
    - [ ] Add bill printing
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
