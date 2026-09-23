#apa-figure(
  table(
    align: (x, y) => if y == 0 {
      center
    } else if x == 0 {
      left
    } else {
      center
    },
    columns: (auto, 1.2fr, 2.4fr, 2.4fr, auto),
    table.header([Test Case], [Module], [Test Description], [Expected Result], [Status]),
    table.hline(stroke: 0.5pt),
    [TC-01], [Authentication], [Log in with a valid manager employee number and password], [User is authenticated and redirected to the manager dashboard], [Pass],
    [TC-02], [Authentication], [Log in with a waiter PIN], [User is authenticated and redirected to the kiosk], [Pass],
    [TC-03], [Authentication], [Log in with an incorrect PIN or password], [Login is rejected with an error message], [Pass],
    [TC-04], [Authentication], [Log out with a valid CSRF token], [Session is cleared and the user is redirected to login], [Pass],
    [TC-05], [Security], [Submit a POST request without a CSRF token], [Request is rejected with a 400 response], [Pass],
    [TC-06], [Access control], [Waiter opens an admin page], [Request is denied with a 403 response], [Pass],
    [TC-07], [Access control], [Head chef opens an admin or bar page], [Request is denied with a 403 response], [Pass],
    [TC-08], [Access control], [Store manager opens the store and inventory pages], [Pages load successfully; admin and reports pages are denied with 403], [Pass],
    [TC-09], [Order processing], [Place a DINE_IN order from the kiosk with several menu items], [Order is saved with its line items and appears in the kitchen queue], [Pass],
    [TC-10], [Order processing], [Place an order containing food and drinks], [Kitchen shows only food items and the bar only drinks], [Pass],
    [TC-11], [Order processing], [Kitchen marks its items served while the bar is still pending], [Order stays open (PLACED) until all stations are served], [Pass],
    [TC-12], [Order processing], [Serving the last station completes the order], [Order is fully served and disappears from the kiosk open orders], [Pass],
    [TC-13], [Order processing], [A waiter views the dashboard open orders], [Only that waiter's own open orders are listed], [Pass],
    [TC-14], [Menu management], [Add a new menu item with ingredient recipe], [Item is saved, VAT is shown, and the item appears with its recipe], [Pass],
    [TC-15], [Menu management], [Edit and deactivate a menu item], [Changes are saved and the item is hidden from the active menu], [Pass],
    [TC-16], [Inventory], [Create an ingredient and add stock], [A movement record is created and the stock page reflects the new quantity], [Pass],
    [TC-17], [Inventory], [Receive water in cases and beef in grams], [Units are converted and the weighted-average cost is computed correctly], [Pass],
    [TC-18], [Inventory], [Add stock with an incompatible unit], [Operation is rejected with an error message], [Pass],
    [TC-19], [Inventory], [Perform a stock take and view the resulting variance], [Variance is calculated from the count versus current stock], [Pass],
    [TC-20], [Payment], [Cashier lists the unpaid orders for the open business day], [All unpaid orders appear in the cashier queue], [Pass],
    [TC-21], [Payment], [Settle an unpaid order by cash], [Payment is recorded, the order becomes PAYED and moves to Settled Today], [Pass],
    [TC-22], [Payment], [Settle with an invalid method or amount], [Payment is rejected and the order remains unpaid], [Pass],
    [TC-23], [Payment], [Print a receipt for a paid order], [The receipt page renders the bill marked as PAID], [Pass],
    [TC-24], [Reporting], [Open the reports page as manager], [Sales by category, hourly sales and orders-by-status reports render], [Pass],
    [TC-25], [Reporting], [Close the open business day], [The day summary is snapshotted and a new day is opened], [Pass],
    [TC-26], [User management], [Create a new user for a role], [Employee number is auto-generated with the correct role prefix and sequence], [Pass],
    [TC-27], [User management], [Create a user with a weak password], [Password is rejected by the validation policy], [Pass],
    table.hline(),
  ),
  note: [All system test cases passed. The full automated suite (167 tests, 573 assertions) is green across the unit, integration and functional layers. Earlier failures — a session CSRF recursion and a bill-view double include — were traced to the two defects, both were fixed and the cases were re-executed successfully.],
  caption: [Summary of System Test Cases],
)