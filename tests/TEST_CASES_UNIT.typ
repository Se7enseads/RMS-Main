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
    [TC-01], [Security], [Request without CSRF-required route skips the guard], [Request is passed through untouched], [Pass],
    [TC-02], [Security], [Submit a POST request with a missing CSRF token], [Request is rejected with a 400 response], [Pass],
    [TC-03], [Security], [Submit a request with an invalid CSRF token], [Request is rejected with a 400 response], [Pass],
    [TC-04], [Security], [Submit a request with a valid CSRF token], [Request passes the CSRF guard], [Pass],
    [TC-05], [Security], [Request on a route without the auth guard], [Request proceeds without authentication], [Pass],
    [TC-06], [Security], [Anonymous user hits an authenticated route], [Request is redirected to the login page], [Pass],
    [TC-07], [Security], [Logged-in user hits an authenticated route], [Request is accepted], [Pass],
    [TC-08], [Session], [Read the session CSRF token twice], [Token is stable per session and 64-char hex], [Pass],
    [TC-09], [Session], [Validate the stored session token], [Stored token is accepted; a wrong token is rejected], [Pass],
    [TC-10], [Session], [Set, get, remove and destroy session values], [Values round-trip, missing keys return null, and destroy clears the session], [Pass],
    [TC-11], [Models], [Map an audit-log row to an Action model], [All fields are mapped with a fallback when the staff name is absent], [Pass],
    [TC-12], [Models], [Map a menu item and an order item row], [All fields are mapped with sensible defaults], [Pass],
    [TC-13], [Models], [Map an order row to the Order model], [Status and type are mapped including a null table id; only CANCELLED is cancelled], [Pass],
    [TC-14], [Models], [Map a user row to the User model], [Fields are mapped and the full name composes with and without a middle name], [Pass],
    table.hline(),
  ),
  note: [All unit test cases passed. The unit layer (17 tests) covers middleware guards, session handling and model mapping, and runs with no database.],
  caption: [Summary of Unit Test Cases],
)