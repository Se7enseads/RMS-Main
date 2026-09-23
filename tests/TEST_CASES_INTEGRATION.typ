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
    [TC-01], [Authentication], [Log in with a valid manager password], [User is authenticated and the role is stored in the session], [Pass],
    [TC-02], [Authentication], [Log in with a valid waiter PIN], [User is authenticated and redirected to the kiosk role home], [Pass],
    [TC-03], [Authentication], [Log in with a wrong password, invalid PIN, inactive or unknown user], [Login is rejected with an error message], [Pass],
    [TC-04], [Authentication], [Log out], [Session is cleared and the current user becomes null], [Pass],
    [TC-05], [Orders · Persistence], [Insert an order with its line items], [Order and items are saved in a single transaction], [Pass],
    [TC-06], [Orders · Persistence], [Insert an order where an item insert fails], [Transaction rolls back and nothing is written], [Pass],
    [TC-07], [Orders · Stations], [Fetch the waiting orders for a station], [Only today's PLACED orders with unserved items are returned, oldest first], [Pass],
    [TC-08], [Orders · Stations], [Fetch waiting orders with items for the other station], [Orders are ignored unless they have unserved items for this station], [Pass],
    [TC-09], [Orders · Stations], [Mark the kitchen and bar station served], [Order transitions to fully served once all stations are marked], [Pass],
    [TC-10], [Orders · Stations], [Mark one station served while another is pending], [Order stays PLACED (open) until the remaining station serves], [Pass],
    [TC-11], [Orders · Stations], [Serve an already-served, cancelled, paid or missing order], [Serving is rejected with an error], [Pass],
    [TC-12], [Orders · Stations], [Query the served orders for today], [Only fully served orders are returned], [Pass],
    [TC-13], [Orders · Placement], [Place a DINE_IN order with several items], [Total is computed and the table id is kept for dine-in orders], [Pass],
    [TC-14], [Orders · Placement], [Place an order with empty or invalid items], [Order is rejected; quantities are clamped to a minimum of one], [Pass],
    [TC-15], [Orders · Retrieval], [Load an order's line items with names], [Items carry their menu names; a missing order resolves to null], [Pass],
    [TC-16], [Permissions], [Check a role's permission by name and by id], [Manager, head chef and waiter have their expected permissions; unknown roles do not], [Pass],
    [TC-17], [Business day], [Read the current open day when none exists today], [Today's business day is created and returned], [Pass],
    [TC-18], [Business day], [Read the day summary], [Summary reflects the orders and payments recorded for the day], [Pass],
    [TC-19], [Business day], [Close the open day], [The day is closed as final and the next day is opened], [Pass],
    [TC-20], [Audit log], [Insert a log entry and query entries], [Entries are written, joined with staff names, ordered newest-first and filterable by user], [Pass],
    [TC-21], [User management], [Create a user for each role], [Employee number is auto-generated with the correct role prefix and running sequence], [Pass],
    [TC-22], [User management], [Create a user with a duplicate national id], [Creation is rejected with an error], [Pass],
    [TC-23], [User management], [Create a user with a weak password], [Passwords that are short, lack a special character, exceed 64 chars, are dictionary words or contain the name/role are rejected], [Pass],
    [TC-24], [User management], [Update a user and change the password], [Employee number and national id are preserved and the password hash is updated with the same validation], [Pass],
    [TC-25], [User management], [Deactivate a user], [The user's active flag is set to false], [Pass],
    table.hline(),
  ),
  note: [All integration test cases passed. The integration layer (71 tests) runs against a copy of the live schema and covers authentication, orders, station routing, permissions, business days, audit logging and user management.],
  caption: [Summary of Integration Test Cases],
)