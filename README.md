# RMS Symphony

A web-based Restaurant Management System (RMS) for small and medium enterprises (SMEs).

## Scope

This system targets small and medium enterprises (SMEs) in the hospitality sector:
single-restaurant operations with a handful of staff and up to a few hundred
orders per day.

An SME is defined here as a business with 1&ndash;50 employees and an annual
turnover below KES 100 million, operating from one or a few sites.

### Out of scope

- **Kibandas**: the system does not cover kibandas (small informal eateries
  typically run by one or two people with minimal infrastructure, limited
  menu variety, and no point-of-sale equipment). Their operational needs
  (street-side service, no staff roles, cash-only micro-transactions) are
  deliberately not supported.
- Multi-branch/chain management, franchise operations, and large-enterprise
  ERP integrations.
- Online food delivery marketplaces (order ingestion from third-party apps).

## Features

- Kiosk: place orders, payments list, printable bills (ReceiptLine receipts)
- Kitchen & bar displays
- Admin: users, roles & permissions, menu, categories, audit logs, reports
- Store: inventory, stock take, variance

## Development

- PHP 8.5, MySQL/MariaDB, Symfony Routing, Tabulator, Playwright (E2E)
- Tests: `vendor/bin/phpunit tests/Unit tests/Integration tests/Functional`
- E2E tests run against a dev server started with
  `RMS_DB_NAME=rms_test php -S localhost:8080 -t public`