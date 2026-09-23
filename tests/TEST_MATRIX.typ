#set text(font: "New Computer Modern", size: 9.5pt)
#set page(margin: 2cm)

#align(center)[
  #text(size: 20pt, weight: "bold")[RMS Symphony — Test Matrix]
  #linebreak()
  #text(size: 11pt, fill: rgb("#555555"))[167 tests · 573 assertions · Unit → Integration → Functional → E2E]
]

#v(4mm)
#table(
  columns: auto,
  inset: 5pt,
  align: (left, left),
  stroke: 0.4pt,
  table.header(strong[Suite], strong[Scope]),
  [#text(fill: rgb("#1a5fb4"))[Unit]],
  [*Core middleware & session security*],
  [#text(fill: rgb("#1a5fb4"))[Functional]],
  [*Kernel — auth / RBAC / full order to serve / pay / close flow against rms_test*],
  [#text(fill: rgb("#1a5fb4"))[Integration]],
  [*Repositories & services on the live schema*],
  [#text(fill: rgb("#1a5fb4"))[E2E]],
  [*Headless-browser journeys via kiosk UI — requires :8080 dev server*],
)

#v(3mm)
#text(size: 8pt, fill: rgb("#777777"))[
  Run: `vendor/bin/phpunit tests/Unit tests/Integration tests/Functional`
  #linebreak()
  E2E requires `RMS_DB_NAME=rms_test php -S localhost:8080 -t public` and `vendor/bin/phpunit tests/E2E`.
]

// ───────────────────────────── Unit ─────────────────────────────
#heading(level: 1, outlined: false, numbering: none)[Unit — pure PHP, no database]

#heading(level: 2, outlined: false, numbering: none)[Core]
#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`Middleware::csrf…`], [CSRF guard: skipped when route has no `_csrf`; missing/invalid token rejected; valid token accepted.],
  [`Middleware::auth…`], [Auth guard: skipped when no `_auth`; anonymous requests rejected; logged-in user accepted.],
  [`Session::csrfToken…`], [Token is stable per session & 64-char hex; validation accepts stored token, rejects wrong one.],
  [`Session::set/get/remove/destroy`], [Round-trips values, missing keys → `null`, remove drops key, destroy clears everything.],
)

#heading(level: 2, outlined: false, numbering: none)[Models]
#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`Action::fromRow`], [Audit-log row mapping incl. fallback `user_name → user_id`; `fromRequest` captures HTTP context.],
  [`MenuItem / OrderItem`], [Row mapping with sensible defaults and optional join-name fallbacks.],
  [`Order::fromRow`], [Status/type mapping incl. nullable table; `isCancelled` only for CANCELLED.],
  [`User::fromRow`], [Full-name composition with and without middle name.],
)

// ───────────────────────────── Integration ─────────────────────────────
#heading(level: 1, outlined: false, numbering: none)[Integration — repositories & services on rms_test]

#heading(level: 2, outlined: false, numbering: none)[Authentication & users]
#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`AuthService::login\*`], [Password + PIN logins succeed; wrong password, inactive user, unknown user, invalid PIN all rejected; logout clears session.],
  [`UserRepository::find\*`], [Lookups by employee number, national id, PIN (present & missing cases); deactivate flips `active`.],
  [`UserService::create\*`], [Creates users; auto-generates sequential employee numbers per role with correct prefixes; rejects duplicates / weak passwords (short, no special char, >64 chars, dictionary, contains name/role) / unknown role.],
  [`UserService::update/deactivate`], [Update keeps employee_num + national_id, changes password with same validation; deactivate works and handles missing users.],
  [`EmployeeNumberGenerator`], [Sequences follow last user, grow past 3 digits; custom roles fall back to first two letters.],
  [`PermissionRepository`], [`hasPermission` by name & id for MANAGER/HEAD CHEF/WAITER and unknown roles.],
)

#heading(level: 2, outlined: false, numbering: none)[Orders, kitchen & bar]
#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`OrderRepository::insertWithItems`], [Inserts order + items in one transaction; rolls back on failure.],
  [`...findWaitingForStationToday`], [Only TODAY's PLACED orders, unserved items for the station, oldest first; other-station items ignored.],
  [`...findServedToday`], [Returns only fully-served orders.],
  [`...markStationServed`], [Transitions PLACED→SERVED when all stations served; stays PLACED while another station is pending; rejects non-PLACED.],
  [`...findItemsByOrderId`], [Items joined with names; missing order → `null`.],
  [`OrderService::placeOrder`], [Computes totals; DINE_IN keeps table id; rejects empty items / invalid items; clamps quantity to >= 1.],
  [`KitchenService::markServed`], [Rejects already-served / cancelled / paid / missing orders; transitions correctly.],
  [`StationSplit`], [Kitchen & bar displays show only their own items; order is served only after both stations; bar rejects served orders; served history shows full receipt.],
)

#heading(level: 2, outlined: false, numbering: none)[Business day & audit]
#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`BusinessDayService`], [Creates today's day when missing; summary reflects orders + payments; closing closes the open day and advances.],
  [`AuditLogRepository`], [Inserts entries; `findAll` joins staff names newest-first; `findByUserId` filters.],
)

// ───────────────────────────── Functional ─────────────────────────────
#heading(level: 1, outlined: false, numbering: none)[Functional — KernelTest over the routed app]

#heading(level: 2, outlined: false, numbering: none)[Auth, RBAC & navigation]
#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`Login / PinLogin / redirects`], [Login page renders; anonymous `/admin` → login; MANAGER/chef/waiter/bartender land on role home; bad credentials rejected; logged-in user hitting login redirects by role.],
  [`CSRF on POST`], [Missing or invalid `csrf_token` on POST → 400; logout requires CSRF; GET logout → 405.],
  [`UnknownRoute`], [`/404` page.],
  [`Forbidden (all roles)`], [WAITER/chef barred from admin pages; head chef from bar; STORE MANAGER gets store+inventory 200 but 403 on `/admin/users` & `/admin/reports`.],
  [`ManagerSeesReportsNav`], [Sidebar shows Reports across admin sub-pages (and Management links on chef/waiter sides).],
  [`AuditLogs`], [Manager sees logs; waiter forbidden.],
)

#heading(level: 2, outlined: false, numbering: none)[Kiosk / kitchen / bar order flow]
#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`KitchenOrderFlowEndToEnd`], [Food order appears on kitchen display → mark served → moves to served history → gone from kiosk open orders.],
  [`BarOrderFlowEndToEnd`], [Drinks-only order skips kitchen, appears on bar, marks served.],
  [`MixedOrderSplitAcrossKitchenAndBar`], [Food+drink split; stays open until both stations served.],
  [`WaiterDashboardOwnOpenOrders`], [Waiter sees own PLACED orders under "Open Orders"; others don't see them (day list unaffected).],
  [`KioskOrderRejectsInvalidItemsJson`], [Malformed items → error page, no order created.],
  [`WaiterCanPrintBill`], [Bill page renders receipt; print is audited; missing order → 404.],
  [`WaiterCanViewPaymentsPage`], [Unpaid orders listed; cash payment moves order to paid, updates totals.],
)

#heading(level: 2, outlined: false, numbering: none)[Menu, inventory & store]
#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`ManagerCan\* item/role/category`], [Create/edit/deactivate flows incl. permission pickers on the role form; waiters are forbidden.],
  [`Category stations`], [Create with KITCHEN/BAR station — drives kitchen vs bar routing.],
  [`Ingredient & stock`], [Create ingredient, add stock; wrong unit rejected; weighted-average cost; receiving in cases (Water) vs grams (Beef); movements recorded; item recipe sees VAT.],
  [`StockTake & Variance`], [Counts vs current stock produce variance; blank counts skipped; dates default; bar stock take only lists bar ingredients; waiters forbidden.],
)

#heading(level: 2, outlined: false, numbering: none)[Close day & cashier]
#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`CloseDay`], [Manager can close the day; summary snapshots orders/payments; advances to next day; waiter forbidden.],
  [`CashierListsAndSettlesUnpaid`], [Unpaid listed; CASH payment sets payment row + PAYED status, moves order into "Settled Today" with badge; print link + `/kiosk/order/{id}/bill` shows PAID.],
  [`CashierPayValidatesAmountAndMethod`], [Invalid method/amount leaves order PLACED with no payment row; missing CSRF on pay → 400; waiter forbidden.],
)

// ───────────────────────────── E2E ─────────────────────────────
#heading(level: 1, outlined: false, numbering: none)[E2E — headless-browser journeys]

#table(
  columns: (1.6fr, 3.4fr),
  inset: 5pt,
  stroke: 0.4pt,
  table.header(strong[Test], strong[Verifies]),
  [`AccessControl`], [Anonymous redirected from admin; waiter & chef blocked from users; waiter blocked from kitchen; manager reaches users.],
  [`WaiterFlow`], [Place a DINE_IN order through the kiosk UI and see it land.],
  [`ChefFlow / BarFlow`], [Kitchen & bar see placed orders and mark them served; bartender places a drinks order from the bar.],
  [`ManagerFlow`], [Create an item then edit & deactivate it; logout works.],
  [`RoleFlow`], [Create a role with permissions, then edit those permissions and see them reflected.],
  [`StoreFlow`], [Create a category, an ingredient, then add stock through the interface.],
  [`BillPrint`], [Bill page renders the receipt as SVG.],
  [`NavHighlight`], [Sidebar highlights the active page across admin and kiosk.],
)