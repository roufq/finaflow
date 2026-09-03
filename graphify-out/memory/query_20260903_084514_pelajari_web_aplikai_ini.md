---
type: "query"
date: "2026-09-03T08:45:14.253177+00:00"
question: "pelajari web aplikai ini"
contributor: "graphify"
outcome: "useful"
source_nodes: ["TransactionService", "DashboardController", "MobileController", "UserScope.php"]
---

# Q: pelajari web aplikai ini

## Answer

Expanded from original query via graph vocab: [application, dashboard, transactions, accounts, categories, budgets, goals, reports, authentication, routes, controllers, models]. FinaFlow is a Laravel 12 personal-finance platform with a Blade web UI and a Flutter mobile client over Sanctum API v1. Its core ledger is Transaction-Account-Category; TransactionService atomically updates balances, triggers behavioral/gamification logic, and invalidates dashboard/report caches. Broader modules cover planning, assets, analytics, integrations, education/coaching, family finance, reporting, RBAC, and 2FA. Data is generally isolated by a UserScope global Eloquent scope.

## Outcome

- Signal: useful

## Source Nodes

- TransactionService
- DashboardController
- MobileController
- UserScope.php