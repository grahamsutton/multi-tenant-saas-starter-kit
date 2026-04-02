# Multi-Tenant SaaS Starter Kit

A production-ready Laravel starter kit for building multi-tenant SaaS applications. Built on Laravel 13, Vue 3, Inertia.js v3, and Stripe billing — everything you need to go from idea to paying customers.

## What's Included

### Multi-Tenancy
- **Organizations** with owner/admin/member roles
- **Organization switching** — users can belong to multiple organizations
- **Prefixed ULIDs** for all model IDs (`usr_`, `org_`, `mem_`, `pln_`)
- Settings page for editing organization name

### Billing & Subscriptions
- **Stripe Checkout** integration via Laravel Cashier
- **Three-tier plan structure** (Starter, Professional, Enterprise) with monthly/annual pricing
- **Free trials** — configurable per plan (Professional gets 14 days by default)
- **Subscription middleware** — gate access to paid features
- **Stripe Customer Portal** — manage payment methods and subscriptions from settings
- Plans seeded automatically during migration

### Authentication
- Login, registration, password reset, email verification
- **Two-factor authentication** (TOTP) with recovery codes
- Password confirmation for sensitive actions
- Rate-limited login attempts
- Strong password requirements in production

### Frontend
- **Vue 3** with TypeScript and Composition API
- **Inertia.js v3** — SPA experience without building an API
- **Tailwind CSS v4** with dark mode
- **Reka UI** (headless components) with shadcn-style primitives
- **Wayfinder** — typed route/action functions, no hardcoded URLs
- Two layout options: sidebar or header (swap with one line)

### Settings
- Profile (name, email, account deletion)
- Organization (name)
- Security (password, 2FA setup)
- Appearance (light/dark/system theme)
- Billing (Stripe Customer Portal redirect)

### Developer Experience
- **Pest v4** test suite with 79+ tests
- **Pint** code formatting
- **ESLint + Prettier** for frontend
- **TypeScript** type checking with `vue-tsc`
- Composer `dev` script runs server, queue, logs, and Vite concurrently
- Composer `ci:check` script for CI pipelines

## Requirements

- PHP 8.3+
- Node.js 20+
- PostgreSQL (recommended) or SQLite
- [Stripe account](https://dashboard.stripe.com/register) with API keys
- Composer 2

## Installation

### 1. Clone and install dependencies

```bash
git clone <repo-url> my-saas-app
cd my-saas-app
composer install
npm install
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database and Stripe credentials:

```env
# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Stripe
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### 3. Set up Stripe products and prices

Create three products in your [Stripe Dashboard](https://dashboard.stripe.com/products) (or via the API) with monthly and annual prices, then add their price IDs to `.env`:

```env
STRIPE_STARTER_MONTHLY_PRICE_ID=price_...
STRIPE_STARTER_ANNUAL_PRICE_ID=price_...
STRIPE_PROFESSIONAL_MONTHLY_PRICE_ID=price_...
STRIPE_PROFESSIONAL_ANNUAL_PRICE_ID=price_...
STRIPE_ENTERPRISE_MONTHLY_PRICE_ID=price_...
STRIPE_ENTERPRISE_ANNUAL_PRICE_ID=price_...
```

### 4. Run migrations

```bash
php artisan migrate
```

This creates all tables and seeds the three default plans.

### 5. Set up Stripe webhooks

For local development, use the [Stripe CLI](https://docs.stripe.com/stripe-cli):

```bash
stripe listen --forward-to your-app.test/stripe/webhook
```

Copy the webhook signing secret to `STRIPE_WEBHOOK_SECRET` in `.env`.

For production, create a webhook endpoint in the Stripe Dashboard pointing to `https://your-domain.com/stripe/webhook` and listening for these events:
- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `invoice.payment_succeeded`
- `invoice.payment_failed`

### 6. Build and serve

```bash
# Development (runs server, queue, logs, and Vite)
composer run dev

# Or with Laravel Herd / Valet
npm run dev
```

### 7. Visit the app

Register a new account at `/register`. You'll create a user and an organization, then be prompted to select a plan before accessing the dashboard.

## Project Structure

```
app/
├── Actions/Fortify/          # Auth actions (registration, password reset)
├── Enums/                    # BillingInterval, OrganizationRole
├── Http/
│   ├── Controllers/
│   │   ├── Billing/          # Plans, Checkout, Success
│   │   └── Settings/         # Profile, Organization, Security, Billing
│   ├── Middleware/            # Inertia, Subscribed, Appearance, ResolveOrganization
│   └── Requests/             # Form validation
└── Models/                   # User, Organization, Membership, Plan

resources/js/
├── actions/                  # Wayfinder-generated controller actions
├── components/               # Vue components (OrganizationSwitcher, etc.)
│   └── ui/                   # shadcn-style primitives
├── layouts/                  # App, Auth, Billing, Settings layouts
├── pages/                    # Inertia page components
│   ├── auth/                 # Login, Register, 2FA, etc.
│   ├── billing/              # Plan selection
│   └── settings/             # Profile, Organization, Security, Appearance
├── routes/                   # Wayfinder-generated route functions
└── types/                    # TypeScript type definitions

tests/
├── Feature/
│   ├── Auth/                 # Authentication tests
│   ├── Billing/              # Plan and checkout tests
│   ├── Models/               # Model relationship and billing tests
│   └── Settings/             # Settings page tests
└── Unit/                     # Unit tests
```

## Switching Layouts

The app ships with two layout options. To switch between them, edit `resources/js/layouts/AppLayout.vue`:

```vue
<!-- Sidebar layout (default) -->
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';

<!-- Header layout -->
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
```

The organization switcher works in both layouts automatically.

## Customizing Plans

Plans are seeded in the `create_plans_table` migration. To modify the default plans, edit the migration before running it, or update plans directly in the database after migration.

Each plan supports:
- `name` and `slug`
- `monthly_price` and `annual_price` (in cents)
- `stripe_monthly_price_id` and `stripe_annual_price_id`
- `trial_days` (0 to disable)
- `is_active` (toggle visibility)
- `sort_order` (display order)

## Running Tests

```bash
# Full suite
php artisan test

# Specific test file
php artisan test --filter=OrganizationSwitchTest

# With code style check
composer test
```

## Code Quality

```bash
# PHP formatting
vendor/bin/pint

# Frontend linting and formatting
npm run lint
npm run format

# TypeScript type checking
npm run types:check

# Full CI check
composer ci:check
```

## License

MIT
