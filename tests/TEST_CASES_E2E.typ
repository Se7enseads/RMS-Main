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
    [TC-01], [Access control], [Anonymous user opens an admin page], [User is redirected to the login page], [Pass],
    [TC-02], [Access control], [Waiter opens a users page], [Page is blocked with access denied], [Pass],
    [TC-03], [Access control], [Head chef opens a users page], [Page is blocked with access denied], [Pass],
    [TC-04], [Access control], [Waiter opens the kitchen display], [Page is blocked with access denied], [Pass],
    [TC-05], [Access control], [Manager opens a users page], [Page loads successfully], [Pass],
    [TC-06], [Waiter flow], [Place a DINE_IN order through the kiosk UI], [Order is created and appears in the system], [Pass],
    [TC-07], [Kitchen flow], [Chef sees a placed food order and marks it served], [Order appears on the kitchen display and then moves to served history], [Pass],
    [TC-08], [Bar flow], [Bartender sees a drinks order and marks it served], [Order appears on the bar display and then moves to served history], [Pass],
    [TC-09], [Bar flow], [Bartender places a drinks-only order from the bar page], [Order is placed and routed to the bar display], [Pass],
    [TC-10], [Manager flow], [Manager creates a menu item, then logs out], [Item is created, saved and the session is cleared], [Pass],
    [TC-11], [Manager flow], [Manager edits and deactivates a menu item], [Changes are saved and the item is hidden from the active menu], [Pass],
    [TC-12], [Role flow], [Manager creates a role with permissions and edits them], [Role and its permission set are reflected in the role edit form], [Pass],
    [TC-13], [Store flow], [Manager creates a category, an ingredient and adds stock], [All are created and stock is recorded], [Pass],
    [TC-14], [Receipts], [Open the bill page for an order], [Receipt renders correctly as an SVG receipt], [Pass],
    [TC-15], [Navigation], [Browse admin and kiosk pages], [The sidebar highlights the active page across both areas], [Pass],
    table.hline(),
  ),
  note: [All end-to-end test cases passed. The E2E layer (15 tests) exercises the real browser against a dev server running on the test database.],
  caption: [Summary of End-to-End Test Cases],
)