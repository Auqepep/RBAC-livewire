# OAuth 2.0 Implementation with Laravel Passport

## 🎯 Overview

Your RBAC application is now an **OAuth 2.0 Authorization Server**! Third-party applications can now:

-   Authenticate users through your system
-   Request access to user data with specific scopes
-   Make API requests on behalf of users
-   Use refresh tokens for long-lived sessions

## 📊 Architecture

```
┌──────────────────────────────────────────────────────────────────────┐
│                     YOUR RBAC APP (Authorization Server)              │
│  - Issues access tokens                                              │
│  - Manages user authentication                                       │
│  - Controls permissions & scopes                                     │
│  - Redis caching for performance                                     │
└──────────────────────────────────────────────────────────────────────┘
                                    ↕
                     OAuth 2.0 Token Exchange
                                    ↕
┌──────────────────────────────────────────────────────────────────────┐
│                     THIRD-PARTY APP (Client Application)              │
│  - Registers as OAuth client                                         │
│  - Requests authorization from users                                 │
│  - Receives access tokens                                            │
│  - Makes API requests with Bearer token                              │
└──────────────────────────────────────────────────────────────────────┘
```

## 🔐 OAuth 2.0 Flow

### Authorization Code Flow (Recommended for Web Apps)

```
1. Third-party app redirects user to:
   GET http://your-rbac-app.com/oauth/authorize
       ?client_id=CLIENT_ID
       &redirect_uri=https://third-party-app.com/callback
       &response_type=code
       &scope=read-user

2. User logs in (if not already)
   → Your OTP authentication system

3. User sees consent screen:
   "Third-Party App wants to access your information"
   [Approve] [Deny]

4. User approves → Redirect to third-party with code:
   https://third-party-app.com/callback?code=AUTH_CODE

5. Third-party exchanges code for tokens:
   POST http://your-rbac-app.com/oauth/token
   {
       "grant_type": "authorization_code",
       "client_id": "CLIENT_ID",
       "client_secret": "CLIENT_SECRET",
       "redirect_uri": "https://third-party-app.com/callback",
       "code": "AUTH_CODE"
   }

6. Response with tokens:
   {
       "token_type": "Bearer",
       "expires_in": 1296000,
       "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
       "refresh_token": "def50200b7..."
   }

7. Third-party makes API requests:
   GET http://your-rbac-app.com/api/user
   Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

## 🚀 Quick Start Guide

### For Your RBAC App (Already Done!)

✅ **Installed Laravel Passport**
✅ **Created OAuth tables** (oauth_clients, oauth_access_tokens, etc.)
✅ **Generated encryption keys**
✅ **Configured API guard**
✅ **Created API endpoints**

### Register a Third-Party OAuth Client

```bash
# Option 1: Password Grant Client (for first-party apps)
php artisan passport:client --password

# Option 2: Authorization Code Client (for third-party apps)
php artisan passport:client

# Follow the prompts:
# - Name: "My Third-Party App"
# - Redirect URL: https://third-party-app.com/oauth/callback
```

**Save the Client ID and Secret!** You'll need these for API requests.

## 📡 API Endpoints

### OAuth Endpoints (Built-in with Passport)

| Method | Endpoint                | Description                    |
| ------ | ----------------------- | ------------------------------ |
| `GET`  | `/oauth/authorize`      | Authorization page for users   |
| `POST` | `/oauth/authorize`      | User approves/denies access    |
| `POST` | `/oauth/token`          | Exchange code for access token |
| `POST` | `/oauth/token/refresh`  | Refresh expired access token   |
| `POST` | `/oauth/token` (revoke) | Revoke access token            |

### Your Custom API Endpoints

| Method | Endpoint      | Auth   | Description                 |
| ------ | ------------- | ------ | --------------------------- |
| `GET`  | `/api/user`   | Bearer | Get authenticated user info |
| `POST` | `/api/logout` | Bearer | Logout & revoke all tokens  |

## 🔑 Grant Types Supported

### 1. **Authorization Code Grant** (Recommended)

Best for: Third-party web applications

```http
POST /oauth/token
Content-Type: application/json

{
    "grant_type": "authorization_code",
    "client_id": "your-client-id",
    "client_secret": "your-client-secret",
    "redirect_uri": "https://your-app.com/callback",
    "code": "received-auth-code"
}
```

### 2. **Password Grant** (First-party apps only)

Best for: Your own mobile/desktop apps

```http
POST /oauth/token
Content-Type: application/json

{
    "grant_type": "password",
    "client_id": "your-client-id",
    "client_secret": "your-client-secret",
    "username": "user@example.com",
    "password": "user-password",
    "scope": "*"
}
```

### 3. **Refresh Token Grant**

Get new access token without re-authentication

```http
POST /oauth/token
Content-Type: application/json

{
    "grant_type": "refresh_token",
    "refresh_token": "your-refresh-token",
    "client_id": "your-client-id",
    "client_secret": "your-client-secret",
    "scope": ""
}
```

## 🧪 Testing with Postman (For OTP-Based Authentication)

### ⚡ Quick Method: Personal Access Token (Recommended!)

Since your app uses **OTP authentication** (no passwords), use Personal Access Tokens for testing:

#### Step 1: Generate Personal Access Token

You already have a personal client! Now generate a token via **Tinker**:

```bash
php artisan tinker
```

**In Tinker, run:**

```php
$user = User::find(1); // Or use your user ID
$token = $user->createToken('Postman Testing')->accessToken;
echo $token;
```

**Example output:**

```
eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5ZDQyNWM4Mi1hYTZjLTRhMWItOWMzZC0xMjM0NTY3ODkwYWIiLCJqdGkiOiIxYTJiM2M0ZDVlNmY3ZzhoOWkwaiIsImlhdCI6MTczMzEyMzQ1NiwiZXhwIjoxNzQ4Njc1NDU2fQ...
```

**📋 Copy this entire token!**

#### Alternative: Generate Token via Database

If you prefer, directly insert into the database:

```bash
php artisan tinker
```

```php
$user = User::find(1);
$token = $user->createToken('My Postman Token', ['*'])->accessToken;
echo "Token: " . $token . "\n";
```

#### Step 3: Test API Endpoints

**A) Get User Information:**

```http
GET http://localhost:8000/api/user
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

**In Postman:**

-   Method: `GET`
-   URL: `http://localhost:8000/api/user`
-   Headers tab → Add:
    -   Key: `Authorization`
    -   Value: `Bearer YOUR_ACCESS_TOKEN_HERE`

**Expected Response:**

```json
{
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "groups": [
        {
            "id": 1,
            "name": "Administrators",
            "role": "Group Admin"
        }
    ],
    "roles": [
        {
            "id": 1,
            "name": "Group Admin",
            "permissions": ["manage_users", "manage_groups"]
        }
    ],
    "permissions": ["manage_users", "manage_groups", "view_dashboard"]
}
```

**B) Test Logout:**

```http
POST http://localhost:8000/api/logout
Authorization: Bearer YOUR_ACCESS_TOKEN
```

**Expected Response:**

```json
{
    "message": "Successfully logged out"
}
```

---

## 🔐 For Third-Party Apps (Real OAuth Flow with OTP)

Third-party apps will use **Authorization Code Flow** which works perfectly with your OTP system:

### How It Works with OTP:

1. **Third-party redirects user to your app**

    ```
    http://localhost:8000/oauth/authorize?client_id=CLIENT_ID&redirect_uri=...
    ```

2. **User logs in via OTP** (your existing auth system)

    - User enters email
    - Receives OTP code
    - Verifies OTP
    - ✅ User is authenticated

3. **User sees consent screen**

    - "Third-Party App wants to access your information"
    - User clicks [Approve]

4. **User redirected back with code**

    ```
    https://third-party-app.com/callback?code=AUTH_CODE
    ```

5. **Third-party exchanges code for token**

    ```http
    POST /oauth/token
    {
        "grant_type": "authorization_code",
        "client_id": "...",
        "client_secret": "...",
        "code": "AUTH_CODE"
    }
    ```

6. **Third-party receives access token** ✅

### Testing Authorization Code Flow

**Step 1: Create OAuth Client**

```bash
php artisan passport:client
# Name: Test Third-Party App
# Redirect: http://localhost:3000/callback
```

**Step 2: Test in Browser**

Visit this URL (replace CLIENT_ID):

```
http://localhost:8000/oauth/authorize?client_id=YOUR_CLIENT_ID&redirect_uri=http://localhost:3000/callback&response_type=code&scope=*
```

-   You'll be redirected to login (if not logged in)
-   Complete OTP verification
-   See consent screen
-   Get redirected with `?code=...` in URL
-   Copy that code

**Step 3: Exchange Code in Postman**

```http
POST http://localhost:8000/oauth/token
Content-Type: application/json
```

**Body:**

```json
{
    "grant_type": "authorization_code",
    "client_id": "YOUR_CLIENT_ID",
    "client_secret": "YOUR_CLIENT_SECRET",
    "redirect_uri": "http://localhost:3000/callback",
    "code": "THE_CODE_FROM_URL"
}
```

**Response:**

```json
{
    "token_type": "Bearer",
    "expires_in": 1296000,
    "access_token": "eyJ0eXAiOiJKV1Q...",
    "refresh_token": "def50200..."
}
```

---

## 🌐 Testing with Browser (Authorization Code Flow)

### Step 1: Create Authorization Code Client

```bash
php artisan passport:client
# Name: Test App
# Redirect: http://localhost:3000/callback
```

**Save output:**

```
Client ID: 9d425c82-aa6c-4a1b-9c3d-1234567890ab
Client Secret: 1a2b3c4d5e6f7g8h9i0j
```

### Step 2: Test Authorization URL

Open in browser:

```
http://localhost:8000/oauth/authorize?client_id=9d425c82-aa6c-4a1b-9c3d-1234567890ab&redirect_uri=http://localhost:3000/callback&response_type=code&scope=*
```

### Step 3: Get Access Token

```bash
curl -X POST http://localhost:8000/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "authorization_code",
    "client_id": "9d425c82-aa6c-4a1b-9c3d-1234567890ab",
    "client_secret": "1a2b3c4d5e6f7g8h9i0j",
    "redirect_uri": "http://localhost:3000/callback",
    "code": "CODE_FROM_CALLBACK"
  }'
```

### Step 4: Test API Request

```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

**Expected Response:**

```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified": true,
    "is_super_admin": false,
    "groups": [
        {
            "group_id": 1,
            "group_name": "Administrators",
            "role_id": 1,
            "role_name": "Admin"
        }
    ],
    "permissions": [
        {
            "id": 1,
            "name": "manage_users",
            "display_name": "Manage Users",
            "group_id": 1,
            "group_name": "Administrators"
        }
    ]
}
```

## 🛠️ Creating a Third-Party App

### Example: React App Integration

```javascript
// config.js
export const OAUTH_CONFIG = {
    clientId: "9d425c82-aa6c-4a1b-9c3d-1234567890ab",
    clientSecret: "1a2b3c4d5e6f7g8h9i0j", // Keep secret on backend!
    redirectUri: "http://localhost:3000/callback",
    authorizationUrl: "http://localhost:8000/oauth/authorize",
    tokenUrl: "http://localhost:8000/oauth/token",
    apiUrl: "http://localhost:8000/api",
};

// Step 1: Redirect to authorization
function loginWithRBAC() {
    const params = new URLSearchParams({
        client_id: OAUTH_CONFIG.clientId,
        redirect_uri: OAUTH_CONFIG.redirectUri,
        response_type: "code",
        scope: "*",
    });

    window.location.href = `${OAUTH_CONFIG.authorizationUrl}?${params}`;
}

// Step 2: Handle callback
async function handleCallback() {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get("code");

    // Exchange code for token (call your backend to keep secret safe)
    const response = await fetch("/api/exchange-token", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code }),
    });

    const { access_token, refresh_token } = await response.json();

    // Store tokens securely
    localStorage.setItem("access_token", access_token);
    localStorage.setItem("refresh_token", refresh_token);
}

// Step 3: Make API requests
async function getUser() {
    const token = localStorage.getItem("access_token");

    const response = await fetch(`${OAUTH_CONFIG.apiUrl}/user`, {
        headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
        },
    });

    return await response.json();
}
```

### Example: Backend Proxy (Node.js/Express)

```javascript
// Protect client secret on backend
app.post("/api/exchange-token", async (req, res) => {
    const { code } = req.body;

    const response = await axios.post("http://localhost:8000/oauth/token", {
        grant_type: "authorization_code",
        client_id: process.env.OAUTH_CLIENT_ID,
        client_secret: process.env.OAUTH_CLIENT_SECRET,
        redirect_uri: process.env.OAUTH_REDIRECT_URI,
        code,
    });

    res.json(response.data);
});
```

## 🔒 Token Lifetimes

Configured in `AppServiceProvider.php`:

-   **Access Token:** 15 days
-   **Refresh Token:** 30 days
-   **Personal Access Token:** 6 months

### Refresh Token Before Expiry

```bash
curl -X POST http://localhost:8000/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "refresh_token",
    "refresh_token": "YOUR_REFRESH_TOKEN",
    "client_id": "YOUR_CLIENT_ID",
    "client_secret": "YOUR_CLIENT_SECRET"
  }'
```

## 📋 Managing OAuth Clients

### List All Clients

```bash
php artisan tinker
>>> \Laravel\Passport\Client::all();
```

### Delete a Client

```bash
php artisan tinker
>>> \Laravel\Passport\Client::find(1)->delete();
```

### Create Client Programmatically

```php
use Laravel\Passport\Client;

Client::create([
    'name' => 'Mobile App',
    'redirect' => 'myapp://oauth/callback',
    'personal_access_client' => false,
    'password_client' => false,
    'revoked' => false,
]);
```

## 🎨 Customizing OAuth Screens

### Custom Authorization View

Create: `resources/views/vendor/passport/authorize.blade.php`

```bash
php artisan vendor:publish --tag=passport-views
```

Then customize the authorization prompt UI to match your brand.

## 🚨 Security Best Practices

### 1. **Keep Client Secrets Secure**

-   Never expose in frontend code
-   Store in environment variables
-   Use backend proxy for token exchange

### 2. **Use HTTPS in Production**

```env
APP_URL=https://your-domain.com
```

### 3. **Implement PKCE for Public Clients**

For mobile/SPA apps without backend:

```http
# Step 1: Generate code verifier & challenge
code_verifier = random_string(128)
code_challenge = base64url(sha256(code_verifier))

# Step 2: Authorization with challenge
GET /oauth/authorize
    ?code_challenge=CODE_CHALLENGE
    &code_challenge_method=S256
    &...

# Step 3: Token with verifier
POST /oauth/token
    "code_verifier": "CODE_VERIFIER"
```

### 4. **Limit Token Scopes**

Define specific scopes instead of using `*`:

```php
// In AppServiceProvider
Passport::tokensCan([
    'read-user' => 'Read user information',
    'read-groups' => 'Read group memberships',
    'manage-profile' => 'Update user profile',
]);
```

### 5. **Monitor Token Usage**

```bash
# View active tokens
php artisan tinker
>>> \Laravel\Passport\Token::where('revoked', false)->count();
```

## 📊 Database Tables

OAuth tables created:

-   `oauth_clients` - Registered client applications
-   `oauth_access_tokens` - Issued access tokens
-   `oauth_refresh_tokens` - Refresh tokens
-   `oauth_auth_codes` - Temporary authorization codes
-   `oauth_device_codes` - Device flow codes

## 🍪 Cookie Configuration (IMPORTANT!)

### Current Development Setup

Your `.env` is configured for **local development**:

```env
SESSION_SECURE_COOKIE=false    # Set to true in production (HTTPS only)
SESSION_HTTP_ONLY=true         # ✅ Prevents JavaScript access (security)
SESSION_SAME_SITE=lax          # ✅ Good for same-domain OAuth
```

### ⚠️ For Production Deployment

**MUST UPDATE** when deploying to production with HTTPS:

```env
SESSION_SECURE_COOKIE=true     # Require HTTPS
SESSION_SAME_SITE=lax          # OR "none" if clients are on different domains
```

### Cookie Behavior Explained

| Setting                       | Value        | Purpose                                             |
| ----------------------------- | ------------ | --------------------------------------------------- |
| `SESSION_SECURE_COOKIE=false` | Development  | Allows HTTP (localhost)                             |
| `SESSION_SECURE_COOKIE=true`  | Production   | Requires HTTPS (secure)                             |
| `SESSION_HTTP_ONLY=true`      | Both         | Prevents XSS attacks                                |
| `SESSION_SAME_SITE=lax`       | Same Domain  | OAuth clients on same domain                        |
| `SESSION_SAME_SITE=none`      | Cross Domain | OAuth clients on different domains (requires HTTPS) |

### Cross-Origin OAuth (Different Domain Clients)

If third-party apps are on **different domains** (e.g., `app1.com` accessing `your-rbac.com`):

1. Set `SESSION_SAME_SITE=none`
2. **MUST** use HTTPS (`SESSION_SECURE_COOKIE=true`)
3. Configure CORS in `config/cors.php`

## 🔍 Debugging

### Enable Passport Debugging

```env
LOG_LEVEL=debug
```

### Check Token Validity

```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -v
```

### Common Issues

**401 Unauthorized:**

-   Token expired
-   Invalid token
-   Missing `Authorization` header

**403 Forbidden:**

-   Token valid but insufficient permissions
-   Check user's groups & roles

**Cookie Issues:**

-   OAuth authorization fails → Check `SESSION_SAME_SITE` setting
-   CSRF token mismatch → Ensure `SESSION_HTTP_ONLY=true`
-   Cookies not sent → Check `SESSION_SECURE_COOKIE` (must match HTTP/HTTPS)

**500 Server Error:**

-   Check Laravel logs: `storage/logs/laravel.log`

## 🚪 Gateway Redirect Integration

### Overview

Your RBAC system now supports **automatic gateway redirect** to third-party applications! When users access the gateway, they can be automatically redirected with authentication details.

### How It Works

```
User clicks "Gateway" → OTP Authentication → Automatic Redirect
                                                    ↓
                                    Third-Party App Receives:
                                    - User ID, Email, Name
                                    - Group ID & Name
                                    - User's Role
                                    - OAuth Client ID
                                    - Timestamp
```

### Configuration (Admin Panel)

Navigate to **Admin → Groups → Edit Group** and scroll to **"Gateway & Third-Party App Integration"** section:

#### Settings:

1. **Enable Gateway Redirect** - Toggle to enable/disable automatic redirect
2. **Third-Party App URL** - Where users will be redirected (e.g., `https://your-app.com/oauth/callback`)
3. **OAuth Client ID** - OAuth 2.0 Client ID for the application (optional)

### Setup New Client Integration

#### Step 1: Get Information From Client

Ask the client for:

-   ✅ **Redirect URL** - Where should users land after authentication
-   ✅ **App Name** - What's the application called (for your records)

#### Step 2: Generate OAuth Client

```bash
php artisan passport:client

# Follow prompts:
Name: Client App Name
Redirect: https://client-app.com/oauth/callback
```

**You'll receive:**

```
Client ID: 019addf9-0c08-7292-a1e8-96378b3ea2ba
Client Secret: lEPjA2aGNI4ed3gkDn2nHrQkC8MzXFXF3XXfZ1ga
```

⚠️ **Save these credentials securely!** The secret won't be shown again.

#### Step 3: Provide Credentials to Client

Send them (via secure channel):

-   Client ID
-   Client Secret
-   OAuth Token Endpoint: `http://your-rbac.com/oauth/token`
-   API Endpoint: `http://your-rbac.com/api/user`

#### Step 4: Configure in Admin Panel

1. Go to **Admin → Groups → Edit**
2. Enable **Gateway Redirect**
3. Set **Third-Party App URL**: `https://client-app.com/oauth/callback`
4. Set **OAuth Client ID**: `019addf9-0c08-7292-a1e8-96378b3ea2ba`
5. **Save**

### What Gets Sent to Third-Party App

When a user is redirected, the following parameters are sent via URL query string:

```
https://client-app.com/oauth/callback
  ?user_id=4
  &user_email=manager@example.com
  &user_name=Manager User
  &group_id=1
  &group_name=Administrators
  &role=administrator
  &role_display=Administrator
  &client_id=019addf9-0c08-7292-a1e8-96378b3ea2ba
  &timestamp=1764661210
```

### Example: Receiving Redirect in Third-Party App

#### PHP Example

```php
<?php
// callback.php
session_start();

// Get user info from URL parameters
$userId = $_GET['user_id'] ?? null;
$userEmail = $_GET['user_email'] ?? null;
$userName = $_GET['user_name'] ?? null;
$groupName = $_GET['group_name'] ?? null;
$role = $_GET['role_display'] ?? null;
$clientId = $_GET['client_id'] ?? null;
$timestamp = $_GET['timestamp'] ?? null;

// Verify timestamp (should be within last 5 minutes for security)
$isValid = (time() - $timestamp) < 300;

if (!$isValid) {
    die('Link expired. Please try again.');
}

// Create session for the user
$_SESSION['user_id'] = $userId;
$_SESSION['user_email'] = $userEmail;
$_SESSION['user_name'] = $userName;
$_SESSION['group'] = $groupName;
$_SESSION['role'] = $role;

echo "Welcome {$userName}! You're logged in as {$role} from {$groupName}";

// Optional: Request OAuth token for API access
// See "Requesting OAuth Token" section below
?>
```

#### Node.js/Express Example

```javascript
// server.js
const express = require("express");
const app = express();

app.get("/oauth/callback", (req, res) => {
    const {
        user_id,
        user_email,
        user_name,
        group_name,
        role_display,
        client_id,
        timestamp,
    } = req.query;

    // Verify timestamp
    const isValid = Date.now() / 1000 - timestamp < 300;

    if (!isValid) {
        return res.status(400).send("Link expired");
    }

    // Create session
    req.session.user = {
        id: user_id,
        email: user_email,
        name: user_name,
        group: group_name,
        role: role_display,
    };

    res.send(`Welcome ${user_name}! Role: ${role_display}`);
});

app.listen(3000);
```

### Requesting OAuth Token from Third-Party App

If you need to make API calls to the RBAC system:

```php
<?php
$clientId = '019addf9-0c08-7292-a1e8-96378b3ea2ba';
$clientSecret = 'lEPjA2aGNI4ed3gkDn2nHrQkC8MzXFXF3XXfZ1ga';

// Request access token using Client Credentials Grant
$ch = curl_init('http://your-rbac.com/oauth/token');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'client_credentials',
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'scope' => '*'
]));

$response = curl_exec($ch);
$data = json_decode($response, true);
$accessToken = $data['access_token'];

// Make API calls
$ch = curl_init('http://your-rbac.com/api/user');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken
]);

$userInfo = curl_exec($ch);
// Returns: {"id":4,"name":"Manager User","groups":[...],"permissions":[...]}
?>
```

### Important: Client ID Management

#### Client ID is NOT Tied to URL

The OAuth Client ID is **permanent and separate** from the redirect URL:

```
✅ Client ID: 019addf9-0c08-7292-a1e8-96378b3ea2ba  ← NEVER changes
✅ Client Secret: lEPjA2aGNI4ed3gkDn2nHrQkC8MzXFXF3XXfZ1ga  ← NEVER changes
✅ Redirect URL: https://client-app.com/callback  ← CAN change anytime
```

#### When You DON'T Need New Client ID:

✅ Client changes their redirect URL  
✅ Client's website goes down temporarily  
✅ Client moves to new domain/hosting  
✅ Switching between dev/staging/production URLs

**Action:** Just update the "Third-Party App URL" in admin panel.

#### When You NEED New Client ID:

❌ Client loses their Client Secret  
❌ Security breach (regenerate for safety)  
❌ Setting up completely separate app/environment  
❌ Client explicitly requests new credentials

### Multiple Environments

For clients with multiple environments (dev/staging/prod):

**Option 1: Single Client ID** (simpler)

-   Use one Client ID for all environments
-   Update redirect URL in admin panel when switching

**Option 2: Multiple Client IDs** (more secure)

```bash
# Development
php artisan passport:client
# Name: Client App (Dev)
# Redirect: http://localhost:3000/callback
# Client ID: abc-123-dev

# Production
php artisan passport:client
# Name: Client App (Prod)
# Redirect: https://client-app.com/callback
# Client ID: xyz-789-prod
```

### Testing the Integration

#### Quick Test with httpbin.org

1. Set redirect URL to: `https://httpbin.org/get`
2. Enable gateway redirect
3. Click "Gateway" button as authorized user
4. You'll see all parameters displayed in JSON format

#### Example Response:

```json
{
    "args": {
        "client_id": "019addf9-0c08-7292-a1e8-96378b3ea2ba",
        "group_id": "1",
        "group_name": "Administrators",
        "role": "administrator",
        "role_display": "Administrator",
        "timestamp": "1764661210",
        "user_email": "manager@example.com",
        "user_id": "4",
        "user_name": "Manager User"
    }
}
```

### Security Best Practices

1. **Verify Timestamp** - Reject links older than 5 minutes
2. **Use HTTPS in Production** - Never send credentials over HTTP
3. **Validate User Data** - Don't trust URL parameters blindly
4. **Store Client Secret Securely** - Use environment variables
5. **Implement HMAC Signature** (optional but recommended):

```php
// Generate signature on RBAC side
$signature = hash_hmac('sha256', $queryString, $clientSecret);

// Verify signature on client side
$expectedSignature = hash_hmac('sha256', $receivedQueryString, $clientSecret);
if (!hash_equals($signature, $expectedSignature)) {
    die('Invalid signature');
}
```

### Troubleshooting

**Redirect Not Working:**

-   Check "Enable Gateway Redirect" is checked
-   Verify "Third-Party App URL" is set correctly
-   Ensure user has proper role/permissions for gateway access

**Parameters Not Received:**

-   Check client app is reading query parameters correctly
-   Verify URL encoding is handled properly

**Invalid Client:**

-   Ensure Client ID matches what's configured in group settings
-   Verify Client Secret is correct (if making token requests)

## 🎉 You're All Set!

Your RBAC system is now a full OAuth 2.0 provider with gateway redirect! Third-party apps can:
✅ Authenticate users through your OTP system
✅ Receive user data automatically via gateway redirect
✅ Request OAuth tokens for API access
✅ Access user permissions with proper authorization
✅ Use refresh tokens for long sessions
✅ Benefit from Redis caching (fast API responses)

---

**Need Help?**

-   Laravel Passport Docs: https://laravel.com/docs/passport
-   OAuth 2.0 Spec: https://oauth.net/2/
