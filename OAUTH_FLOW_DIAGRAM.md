# OAuth 2.0 Flow Diagram - RBAC Livewire Implementation

## Overview

Implementasi OAuth 2.0 Authorization Code Flow dengan PKCE dan Auto-Approval untuk SSO experience.

---

## Flow Diagram (Mermaid)

```mermaid
sequenceDiagram
    participant User as User
    participant ClientApp as Client App
    participant AuthServer as Auth Server (RBAC)
    participant ResourceServer as Resource Server

    Note over User,ResourceServer: 1. User Login ke RBAC
    User->>AuthServer: Klik Login
    AuthServer->>User: Kirim OTP ke Email
    User->>AuthServer: Input OTP
    AuthServer->>ResourceServer: Verify OTP
    ResourceServer-->>AuthServer: User Data

    Note over User,ResourceServer: 2. Authorization Request
    User->>AuthServer: Klik Gateway Button (Client App)
    AuthServer->>AuthServer: Auto-Approve (no consent)
    AuthServer-->>ClientApp: Redirect + Authorization Code

    Note over User,ResourceServer: 3. Token Exchange
    ClientApp->>AuthServer: POST /oauth/token (code, secret, PKCE)
    AuthServer->>AuthServer: Validate PKCE + Client
    AuthServer-->>ClientApp: Access Token + Refresh Token

    Note over User,ResourceServer: 4. Access User Info
    ClientApp->>AuthServer: GET /api/user (Bearer Token)
    AuthServer->>ResourceServer: Query User Data
    ResourceServer-->>AuthServer: User Record
    AuthServer-->>ClientApp: User Data (id, name, email)
    ClientApp->>ClientApp: Auto-login User
```

---

## Key Features

### Sesuai OAuth 2.0 Standard

-   Authorization Code Flow
-   PKCE (S256)
-   State Parameter (CSRF protection)
-   Client Authentication
-   Refresh Token Rotation

### Custom SSO Features

-   Auto-Approval (no consent screen)
-   OTP Login (email-based)
-   Cache-based Token Storage

---

## Endpoints

| Endpoint           | Method | Purpose                  |
| ------------------ | ------ | ------------------------ |
| `/oauth/authorize` | GET    | Authorization request    |
| `/oauth/token`     | POST   | Token exchange & refresh |
| `/api/user`        | GET    | Get user info            |

---

## Notes

-   Auto-approval di step 2 adalah custom feature untuk SSO
-   Refresh token rotation untuk security
-   OTP login sebagai authentication method
