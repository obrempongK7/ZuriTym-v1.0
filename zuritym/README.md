# ZuriTym — Complete Reward Earning App
## Full Stack: Laravel Backend + Android App (Java)

---

## 📁 Project Structure

```
zuritym/
├── zuritym-backend/     ← Laravel 10 API + Admin Panel
└── zuritym-android/     ← Android Java App
```

---

## 🚀 BACKEND SETUP (Laravel)

### Requirements
- PHP 8.1+, Composer, MySQL 8.0+, Node.js 18+

### Step 1 — Install dependencies
```bash
cd zuritym-backend
composer install
cp .env.example .env
php artisan key:generate
```

### Step 2 — Configure .env
```
DB_DATABASE=zuritym_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
APP_URL=https://yourdomain.com

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

# Firebase (for push notifications)
FIREBASE_CREDENTIALS=storage/firebase/firebase-credentials.json
```

### Step 3 — Database setup
```bash
php artisan migrate
php artisan db:seed
```

### Step 4 — Storage link
```bash
php artisan storage:link
```

### Step 5 — Register Middleware (app/Http/Kernel.php)
Add to `$routeMiddleware`:
```php
'admin.auth' => \App\Http\Middleware\AdminAuth::class,
```

### Step 6 — Register Commands (app/Console/Kernel.php)
```php
protected $commands = [
    \App\Http\Commands\RefreshLeaderboard::class,
];

protected function schedule(Schedule $schedule): void
{
    $schedule->command('zuritym:refresh-leaderboard')->hourly();
}
```

### Step 7 — Run
```bash
php artisan serve
# Admin panel: http://localhost:8000/admin
# API base:    http://localhost:8000/api/v1/
```

### Default Admin Login
- **Email:** admin@zuritym.com
- **Password:** Admin@123456

---

## 📱 ANDROID APP SETUP

### Requirements
- Android Studio Hedgehog or newer
- JDK 11+, Android SDK 34

### Step 1 — Open in Android Studio
1. Open Android Studio → **Open Project**
2. Select `zuritym-android/`

### Step 2 — Configure API URL
In `app/build.gradle`:
```groovy
buildConfigField "String", "BASE_URL", '"https://yourdomain.com/api/v1/"'
```

### Step 3 — Google Sign-In
1. Create a project at console.firebase.google.com
2. Add Android app with package `com.zuritym.app`
3. Download `google-services.json` → place in `app/`
4. Update `strings.xml` with your `default_web_client_id`

### Step 4 — AdMob Setup
In `strings.xml`:
```xml
<string name="admob_app_id">ca-app-pub-YOUR_APP_ID</string>
```

### Step 5 — Build & Run
```
Build → Generate Signed APK (for production)
Run → Run 'app' (for debug)
```

---

## 🔑 KEY FEATURES IMPLEMENTED

### Backend API
| Endpoint | Description |
|---|---|
| POST /auth/register | Register + anti-fraud check |
| POST /auth/login | Login with device tracking |
| POST /auth/google | Google OAuth login |
| GET  /home | Dashboard data |
| GET  /wallet/balance | User balance |
| POST /wallet/redeem-promo | Apply promo code |
| POST /wallet/withdraw | Request withdrawal |
| GET  /tasks | All tasks |
| POST /tasks/{id}/start | Start a task |
| POST /tasks/{id}/complete | Complete a task |
| GET  /spin/config | Spin wheel segments |
| POST /spin/spin | Perform a spin |
| POST /scratch/issue | Get scratch card |
| POST /scratch/{id}/scratch | Scratch a card |
| GET  /offerwalls | Available offerwalls |
| POST /postback/{slug} | Offerwall postback (unauthenticated) |
| GET  /leaderboard | Rankings (all/weekly/monthly) |
| GET  /chat/messages | Chat history |
| POST /chat/send | Send chat message |

### Admin Panel
| Section | Capabilities |
|---|---|
| Dashboard | Stats, charts, pending items |
| Users | View, edit, block, credit/debit wallet |
| Tasks | Full CRUD, enable/disable |
| Withdrawals | Approve/reject with auto refund |
| Offerwalls | CRUD + postback config |
| Spin Wheel | Segment management with probabilities |
| Promo Codes | Generate, set limits, expiry |
| Notifications | Send push to all/specific users |
| Settings | App config, limits, announcements |
| Ad Networks | AdMob, AppLovin, Unity, etc. |
| Reports | Transaction export to CSV |

### Android App
| Screen | Features |
|---|---|
| Splash | Auto-login check |
| Login | Email+password, Google Sign-In |
| Register | Referral code support |
| Home | Balance, quick actions, featured tasks |
| Tasks | All task types, complete with timer |
| Earn | Spin, Scratch, Offerwall, Chat |
| Wallet | Balance, transactions, promo codes |
| Withdraw | Multi-method withdrawal |
| Leaderboard | All-time, weekly, monthly tabs |
| Profile | User info, logout |
| Chat | Global chatroom |
| WebView | Tasks & offerwall web integration |

---

## 🛡️ Anti-Fraud System

- **IP-based limit:** Max 2 accounts per IP (configurable)
- **Device-based limit:** Max 1 account per device ID
- **Fraud scoring:** Auto-increment for suspicious activity
- **Auto-block:** Users scoring 100+ are auto-blocked
- **Wallet locking:** Blocked users have wallets frozen
- **Login device tracking:** Alerts on device mismatch

---

## 💰 Ad Networks Supported

Configure in Admin → Ad Networks:

| Network | Type |
|---|---|
| Google AdMob | Banner, Interstitial, Rewarded |
| AppLovin | Banner, Interstitial, Rewarded |
| Facebook Audience | Banner, Interstitial |
| Unity Ads | Banner, Interstitial, Rewarded |
| IronSource | Banner, Interstitial, Rewarded |
| Wortise | Banner, Interstitial, Rewarded |
| Vungle | Banner, Interstitial, Rewarded |
| AppLovin MAX | Mediation + Bidding |

---

## 🌐 Offerwall Integration

Three integration types supported:
1. **Web** — Load offerwall URL in WebView with user params
2. **API** — Direct API integration
3. **SDK** — Native SDK (custom implementation per network)

Postback URL: `https://yourdomain.com/api/v1/postback/{offerwall-slug}`

Parameters received: `user_id`, `payout`, `offer_id`, `transaction_id`

---

## ⚙️ Cron Jobs

Add to server crontab:
```bash
* * * * * cd /path/to/zuritym-backend && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks:
- Leaderboard refresh (every hour)

---

## 📞 Support & Customization

- Change app name: Update `app_name` in `strings.xml` and `.env`
- Change colors: Update `colors.xml` and CSS variables in admin layout
- Add payment method: Admin → Settings → Payment Methods
- Configure withdrawal limits: Admin → Settings → Wallet Settings

---

*ZuriTym v1.0 — Built with Laravel 10 + Android Java*
