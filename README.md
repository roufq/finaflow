# FinaFlow - Comprehensive Personal Finance Management System

FinaFlow is a comprehensive personal finance management web application built with Laravel 12. This application helps users track income, expenses, investments, assets, debts, and manage all aspects of their personal finances easily and insightfully.

## 🚀 Key Features

### 💰 **Personal Finance Foundation**
- **Dashboard**: Financial summary with data visualizations and cash flow projections
- **Transaction Management**: Add, edit, delete income and expense transactions
- **Categories**: Manage categories to organize transactions (income/expense)
- **Account Management**: Multiple account types (bank, cash, credit card, e-wallet, investment)
- **Transfer Management**: Transfer between accounts with complete history

### 🎯 **Financial Planning & Goals**
- **Goal Setting**: Financial goals setup with progress tracking
- **Budget Management**: Zero-based budgeting with templates (50/30/20 rule)
- **Debt Management**: Debt payoff calculators with snowball/avalanche methods

### 📈 **Investment & Asset Tracking**
- **Investment Portfolio**: Stock, mutual fund, crypto tracking with ROI calculations
- **Asset Management**: Real estate, vehicle, personal assets with depreciation tracking
- **Net Worth Dashboard**: Real-time net worth calculation with health score

### 🧠 **Behavioral Finance & Lifestyle**
- **Spending Triggers**: Identify spending patterns based on emotions and situations
- **Habit Formation**: Savings and spending habit tracking with streak counters
- **Financial Personality**: Financial personality assessment with personal recommendations
- **Gamification**: Points system, badges, and achievements for financial motivation
- **Subscription Management**: Subscription tracking with cost optimization and cancellation reminders
- **Reward & Loyalty**: Credit card rewards management and loyalty points programs

### Automation & Integration
- **Smart Automation**: Condition/action builder, manual run & toggle, and automated bill reminders
- **Bank Integration**:
  - CRUD bank connections with multiple account types (checking, savings, credit_card)
  - Import transactions via CSV, OFX, or API with auto-categorization
  - Real-time balance synchronization with duplicate transaction detection
  - Integration dashboard with sync status and balance tracking
- **API Integrations**: Credit score integration, investment data, financial news, and weather with rate limit tracking
- **Integration Tools**: Receipt OCR, voice-to-text, email/invoice parser, and other quick entry tools

### 👨‍👩‍👧‍👦 **Family Finance**
- **Shared Expenses**: Family shared expense tracking with split methods (equal/percentage/custom)
- **Family Goals**: Joint family financial targets
- **Gift Events**: Budget planning and tracking for special events
- **Family Members**: Family member management for joint budgeting

### Financial Coaching & Literacy
- **Personalized Action Plans**: Automated monthly action plans prioritizing debts, emergency funds, and investments based on user conditions
- **Weekly Accountability Checklist**: Weekly checklists with statuses & reminders to keep plans on track
- **Micro-Learning Hub**: Recommended short articles, videos, and quizzes based on financial personas while tracking comprehension scores
- **Financial Journaling**: Record reflections, moods, and commitments linked to habits or spending triggers
- **Coach / HR Export**: Export ready-to-send financial health summaries for coaches, HR, or corporate wellness programs

### Financial Education Hub
- **Curated Educational Modules**: Multi-language library with complete metadata (category, level, estimated time, tags, learning objectives, resources)
- **Learning Path Personalization**: Recommendation engine that reads transactions, goals, habits, triggers, and financial personas to determine the next learning focus
- **Financial News Pipeline**: Automated financial news synchronization via APIs or local curation with preference tagging and built-in cron scheduling
- **Community Stories & Moderation**: Peer success story submissions with heuristic auto-moderation and approval history
- **Progress & UX Tracking**: Progress insights, profile recommendations, and UX checklists ensuring educational content remains relevant and actionable

### ⚙️ **Additional Features**
- **Multi-Language Support**: Indonesian and English
- **Settings**: Currency configurations and other personal preferences
- **Authentication**: Secure login and registration system
- **Responsive Design**: Responsive interface for desktop and mobile with a collapsible sidebar
- **User-Scoped Data**: All data is securely isolated per user

## 🛠️ Technologies Used

- **Framework**: Laravel 12
- **Database**: MySQL/SQLite
- **Frontend**: Bootstrap 4 (SB Admin 2 Template) / TailwindCSS Elements
- **Authentication**: Laravel Sanctum
- **Icons**: Font Awesome

## 📋 System Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM (for asset compilation)
- MySQL or SQLite

## ⚡ Installation

1. **Clone Repository**
   ```bash
   git clone https://github.com/your-username/finaflow.git
   cd finaflow
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Set `FINANCIAL_NEWS_ENDPOINT` and `FINANCIAL_NEWS_API_KEY` if using external news sources.

4. **Database Setup**
   ```bash
   # Configure the database in the .env file
   php artisan migrate
   php artisan db:seed
   
   # Seed curated educational modules (optional)
   php artisan db:seed --class=EducationModuleSeeder
   ```

5. **Compile Assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

6. **Run Application**
   ```bash
   php artisan serve
   ```

   Access the application at: `http://localhost:8000`

7. **Financial News Synchronization (optional)**
   ```bash
   php artisan financial:sync-news
   ```

   Run this command immediately to populate the news table. The scheduler (`php artisan schedule:work`) runs automatically twice a day to keep content fresh.

## 🔐 Demo Accounts

For testing, use the following accounts:
- **Email**: `john@example.com` or `jane@example.com` / `admin@finaflow.test`
- **Password**: `password`

## 🔐 Role & Access Control

- The default **user** role only has access to the dashboard; further permissions are assigned per user by the administrator.
- New users automatically have basic access: Dashboard, Transactions/Tags, Accounts/Transfers, Budgets, and Goals. Admins can restrict this via the Manage Users page.
- Admins can manage roles, activate/deactivate users, and restrict sidebar menus per user on the **Admin → Manage Users** page (`/admin/users`).
- Available permissions and protected menus:
  - `access dashboard`: Dashboard.
  - `manage transactions`: Transactions & Tags.
  - `manage accounts`: Accounts & Transfers.
  - `manage budgets`: Budgets & Debts.
  - `manage goals`: Goals.
  - `view reports`: Reporting, Analytics, Investments, Assets, Net Worth, Tax Documents.
  - `manage automations`: Automations & Integrations.
  - `manage family`: Subscriptions & Rewards/Loyalty.
  - `manage behavioral`: Behavioral Insights, Education, Coaching.
- Deactivated users can still log in but will see a warning overlay and cannot access any pages.

## 📊 Database Structure

### Main Tables:
- **users**: User data
- **settings**: User settings (currency, starting month)
- **categories**: Transaction categories (income/expense)
- **transactions**: Financial transaction data
- **accounts**: Account management (bank, cash, credit card, etc.)
- **transfers**: Inter-account transfers
- **goals**: User financial targets
- **budgets**: Expenditure budgets
- **debts**: Debt management
- **investments**: Investment portfolios
- **assets**: Physical asset management
- **tags**: Transaction tags
- **action_plans**: Monthly action plans
- **action_plan_tasks**: Weekly checklists with statuses & reminders
- **micro_learnings**: Educational content (articles, videos, quizzes)
- **micro_learning_progress**: Micro-learning progress & scores
- **financial_journal_entries**: Reflections & coaching commitments
- **community_stories**: Community stories for peer learning

### Behavioral Finance Tables:
- **spending_triggers**: Spending trigger patterns
- **habits**: Financial habits with streak tracking
- **financial_personalities**: Financial personality assessments
- **gamifications**: Points, badges, and achievements
- **subscriptions**: Subscription and recurring cost tracking
- **rewards**: Credit card rewards
- **loyalty_programs**: Loyalty points programs

### Automation & Integration Tables:
- **automations**: Automated rules for alerts and reminders
- **bank_integrations**: Bank account integrations
- **api_integrations**: External API integrations

### Family Finance Tables:
- **family_members**: Family member data
- **shared_expenses**: Shared expenses with split methods
- **family_goals**: Family financial targets
- **gift_events**: Gift budget planning for events

### Relations:
- User hasMany: Settings, Categories, Transactions, Accounts, Transfers, Goals, Budgets, Debts, Investments, Assets, SpendingTriggers, Habits, FinancialPersonalities, Gamifications, Subscriptions, Rewards, LoyaltyPrograms, Automations, BankIntegrations, ApiIntegrations, FamilyMembers, SharedExpenses, FamilyGoals, GiftEvents
- Category hasMany Transactions
- Account hasMany Transactions, Transfers
- Transaction belongsTo Category, Account
- Transfer belongsTo FromAccount, ToAccount
- Investment/Asset belongsTo User
- FamilyMember hasMany SharedExpenses
- SharedExpense belongsToMany FamilyMembers

## 🗂️ Project Structure

```
finaflow/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/
│   │   ├── DashboardController.php
│   │   ├── SettingController.php
│   │   ├── CategoryController.php
│   │   ├── TransactionController.php
│   │   ├── AccountController.php
│   │   ├── TransferController.php
│   │   ├── GoalController.php
│   │   ├── BudgetController.php
│   │   ├── DebtController.php
│   │   ├── InvestmentController.php
│   │   ├── AssetController.php
│   │   ├── TagController.php
│   │   └── NetWorthController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Setting.php
│   │   ├── Category.php
│   │   ├── Transaction.php
│   │   ├── Account.php
│   │   ├── Transfer.php
│   │   ├── Goal.php
│   │   ├── Budget.php
│   │   ├── Debt.php
│   │   ├── Investment.php
│   │   ├── Asset.php
│   │   ├── Tag.php
│   │   └── Scopes/UserScope.php
│   └── Policies/
│       ├── AccountPolicy.php
│       ├── BudgetPolicy.php
│       ├── DebtPolicy.php
│       └── GoalPolicy.php
├── database/
│   ├── migrations/
│   │   └── [20+ migration files]
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php
│   │   ├── auth/
│   │   ├── dashboard.blade.php
│   │   └── ...
│   └── css/
├── routes/
│   └── web.php
└── public/
```

## 🎯 Usage

1. **Login/Register**: Create an account or log in with demo credentials
2. **Dashboard**: View your financial summaries
3. **Settings**: Adjust preferred currency limits and reporting month start
4. **Categories**: Pre-populated via seeders but customizable
5. **Transactions**: Log income and expenses daily

## 🔧 Artisan Commands

```bash
# Database migrations
php artisan migrate

# Seed database with dummy data
php artisan db:seed

# Reset and re-seed database
php artisan migrate:fresh --seed

# Run dev server
php artisan serve

# Generate application key
php artisan key:generate

# Clear application caches
php artisan optimize:clear
```

### Additional Commands
- `php artisan bank:sync-integrations` — Sync active bank integration transactions & balances (runs hourly via scheduler).

### Two-Factor Authentication (TOTP)
- **Setup**: Login → Profile Menu → Two-Factor Auth (`/twofactor/setup`), scan the QR in an authenticator app, and enter a 6-digit code to activate. Backup codes are available on the same page.
- **Login**: If 2FA is active and the device isn't remembered, enter a 6-digit code or backup code. Check "Remember this device" to skip OTP for 30 days.

## 📱 API Endpoints

The application utilizes stateful RESTful routes with Laravel Sanctum authentication:

### Authentication Routes:
- `GET /` - Redirects to dashboard or login
- `GET/POST /login` - Authentication
- `GET/POST /register` - Registration
- `POST /logout` - Logout

*(Consult `routes/web.php` and `routes/api.php` for complete endpoint references)*

## 🤝 Contribution

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the Envato Regular / Extended License limits. Please review the licensing agreement on your purchase receipt or the corresponding License folder.

## 📞 Support

If you run into issues or have questions:
1. Ensure your server matches the minimal specs
2. Refer to the specific `DEPLOYMENT_GUIDE.md` for production queries
3. Open a support ticket via the Envato author dashboard

---

**Built with ❤️ using the Laravel Framework**
