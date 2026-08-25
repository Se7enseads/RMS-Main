# TODOs
 - [x] Outline in the scope that it does not cover kibandas
 - [x] Define Small and Medium Enterprises

## Feature List TODOs

1. [ ] Add Admin close day button
    - [x] Role Creation should include a selection for available permissions
    - [x] Fix issue with sorting in status
    - [ ] Voiding of orders by (Owner, Manager if right is given)
    - [x] Variance display
        - [x] After daily stock-take a variance is performed to
2. [x] Store display
    - [x] Insert the individual ingredients to the store
    - [x] Store admin page.
        - [x] Variance display
2. [x] Add Kitchen Display
    - [x] food should be displayed here
3. [x] Add Bar Display
    - [x] Drinks should go to the bar display
3. [ ] Add Cashier Display
    - [ ] Add Payment Display and receipt printing (integrate Daraja API)
4. [x] Add Reports in Admin
    - [x] Use Tabulator for table printing and download/export functions.
    - [x] Report queries defined as SQL views (v_sales_by_day, v_item_sales_by_day, v_payments_by_day)
5. [x] Add Kiosk:
    - [x] Add bill printing
    - [x] Payment display for the day
    - [x] Dashboard UI polish (from inline TODOs in `src/Views/kiosk/dashboard.php`):
        - [x] Stacked sections instead of side-by-side; remove open-orders duplication (invisible table)
        - [x] Fix dashboard-header alignment
        - [x] Order card style + status button style
        - [x] Pluralize "items" when count > 1
        - [x] Show eating status (dine-in vs takeaway)
6. Add Voiding

- [x] Transactions on all DML (insert, update, delete)
    - [x] add CSRF tokens
- [x] Proper error handling Frontend and Backend
    - [x] Dismissable errors
    - [x] Timeout frontend errors

- [x] Testing
    - [x] Unit tests
    - [x] HTTP tests

## CHECKLIST

Check if all routes:

- [x] have proper auth
- [x] have proper CSRF protection
    - [x] delete and reset state, if session is wrong
- [x] have necessary permissions
    - [x] delete and reset state, if session is wrong
- [x] use semantic HTML
  - [x] details for accordion
  - [x] dialog for modals
- [x] make sure titles match up
