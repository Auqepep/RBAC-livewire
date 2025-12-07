# 📘 FAQ OAuth Client Management

## ❓ Pertanyaan Umum

### **Q1: Gimana cara dapetin Client Secret?**

**A:** Client Secret **hanya ditampilkan SEKALI** waktu bikin client baru!

```
Alur:
1. Admin klik "Buat Client Baru"
2. Isi form → Klik "Buat Client"
3. Modal muncul dengan:
   ✓ Client ID
   ✓ Client Secret (⚠️ HANYA SEKALI INI!)
4. Klik "📋 Salin" untuk copy
5. Simpan di .env atau secret manager
6. Klik "✓ Sudah Saya Simpan"

⚠️ Secret TIDAK BISA dilihat lagi setelah modal ditutup!
⚠️ Secret di-hash di database (tidak bisa di-retrieve)
```

**Kalau kehilangan Secret:**

-   Admin harus **regenerate** secret baru
-   Third-party app harus **update** secret baru di config mereka

---

### **Q2: 1 Client ID bisa punya banyak Redirect URI?**

**A:** **IYA BISA!** Bahkan **SANGAT DISARANKAN!** 🎯

```
1 Client ID = BANYAK Redirect URI ✅

Contoh:
Client ID: 3c1c109b-8ab4-4874-8819-330dfa13e02c

Redirect URIs:
✓ https://myapp.com/auth/callback          (Production)
✓ https://staging.myapp.com/auth/callback  (Staging)
✓ http://localhost:3000/callback           (Development)
✓ myapp://callback                         (Mobile Deep Link)
```

**Keuntungan Multiple URI:**

1. ✅ **Development + Staging + Production** pakai 1 client
2. ✅ **Web + Mobile** app pakai 1 client
3. ✅ **Flexible** deployment tanpa bikin client baru
4. ✅ **Manageable** - semua environment terpusat

---

### **Q3: Kenapa harus exact match Redirect URI?**

**A:** **Security!** Mencegah Open Redirect Attack.

```
❌ SALAH:
Registered: https://app.com/callback
Request:    https://evil.com/callback?next=steal

✅ BENAR:
Registered: https://app.com/callback
Request:    https://app.com/callback  (exact match!)
```

**OAuth Server akan reject** kalau redirect URI tidak exact match!

---

### **Q4: Berapa banyak Redirect URI yang bisa ditambahkan?**

**A:** **Tidak ada limit!** Tambah sebanyak yang dibutuhkan.

```
Praktik Terbaik:
- Development:  1-2 URIs (localhost)
- Staging:      1 URI
- Production:   1-2 URIs (primary + backup)
- Mobile:       1 URI per platform (myapp://callback)

Total sekitar 5-10 URIs per client (reasonable)
```

**Tapi jangan spam!** Cuma tambah yang benar-benar dipakai.

---

### **Q5: Bisa ganti Client ID setelah dibuat?**

**A:** **TIDAK BISA!** Client ID **permanent**.

```
Yang BISA diubah:
✓ Nama Aplikasi
✓ Redirect URIs (tambah/hapus)
✗ Client ID (permanent UUID)
```

**Kalau butuh Client ID baru:**

-   Buat client baru
-   Update third-party app dengan credentials baru
-   Revoke client lama

---

### **Q6: Perbedaan Client ID vs Client Secret?**

| Aspek               | Client ID           | Client Secret        |
| ------------------- | ------------------- | -------------------- |
| **Fungsi**          | Identifier public   | Autentikasi private  |
| **Boleh di-share?** | ✅ Ya (public)      | ❌ Tidak (rahasia!)  |
| **Di URL?**         | ✅ Ya (query param) | ❌ Tidak (body POST) |
| **Stored where?**   | Frontend OK         | Backend only!        |
| **Format**          | UUID (visible)      | Hashed (hidden)      |

```javascript
// ✅ BENAR - Client ID di URL (public)
window.location = "https://oauth.com/authorize?client_id=abc123";

// ❌ SALAH - Secret di URL (JANGAN!)
// Client Secret hanya untuk server-to-server communication!
```

---

### **Q7: Kapan pakai HTTPS vs HTTP untuk Redirect URI?**

**A:**

```
Production:  HTTPS WAJIB! ✅
Staging:     HTTPS recommended
Development: HTTP localhost OK ✅

✅ Production:
https://myapp.com/callback

✅ Development:
http://localhost:3000/callback
http://127.0.0.1:8000/callback

❌ Development dengan domain (DANGER!):
http://myapp.com/callback  (man-in-the-middle attack!)
```

---

### **Q8: Format Redirect URI yang valid?**

**A:**

```
✅ VALID:
https://example.com/auth/callback
https://api.example.com/oauth/redirect
http://localhost:3000/callback
http://127.0.0.1:8000/auth
myapp://oauth-callback (mobile deep link)
com.myapp://callback (Android app link)

❌ INVALID:
example.com/callback           (no protocol)
https://example.com/*          (wildcard)
https://*.example.com/callback (wildcard subdomain)
/callback                      (relative path)
```

---

### **Q9: Kalau kehilangan Secret, gimana?**

**A:** Admin harus **regenerate**!

```
Step-by-step:
1. Admin login → /admin/oauth-clients
2. Klik "Edit" pada client
3. (Future feature) Klik "Regenerate Secret"
4. Secret baru di-generate & ditampilkan (sekali lagi!)
5. Admin kirim secret baru ke third-party
6. Third-party update .env mereka
7. Secret lama otomatis invalid

⚠️ WARNING: Token lama tetap valid sampai expired!
```

**Alternatif sekarang:**

-   Revoke client lama
-   Buat client baru dengan nama & URI yang sama

---

### **Q10: Kenapa Secret di-hash, bukan encrypted?**

**A:** **Security best practice!**

```
Hash (one-way):
Plain Secret → Hash → Database
              ↓
         Tidak bisa di-reverse!

Verify:
User input → Hash → Compare dengan database hash
                    ↓
               ✅ Match? Login!

Keuntungan:
✓ Kalau database bocor, secret asli tetap aman
✓ OAuth server sendiri tidak tahu secret asli
✓ Harus punya plain secret untuk autentikasi
```

---

### **Q11: Contoh implementasi client dengan multiple URIs?**

**A:**

```javascript
// .env file
OAUTH_CLIENT_ID=3c1c109b-8ab4-4874-8819-330dfa13e02c
OAUTH_CLIENT_SECRET=abc123xyz...

// Pilih redirect URI based on environment
const REDIRECT_URI = process.env.NODE_ENV === 'production'
  ? 'https://myapp.com/auth/callback'      // Production
  : process.env.NODE_ENV === 'staging'
    ? 'https://staging.myapp.com/callback' // Staging
    : 'http://localhost:3000/callback';    // Development

// OAuth request
const authUrl = `${OAUTH_SERVER}/oauth/authorize?` +
  `client_id=${OAUTH_CLIENT_ID}&` +
  `redirect_uri=${encodeURIComponent(REDIRECT_URI)}&` +
  `response_type=code&` +
  `scope=read-user read-groups`;
```

**Semua environment pakai 1 Client ID yang sama!** ✅

---

### **Q12: Berapa lama Secret berlaku?**

**A:** **Selamanya!** (sampai di-revoke atau di-regenerate)

```
Token expiry:
✓ Access Token:  15 hari
✓ Refresh Token: 30 hari

Client Secret expiry:
✓ PERMANENT (never expire)
  Kecuali:
  - Di-revoke manual oleh admin
  - Di-regenerate (secret baru)
  - Client di-delete
```

**Best practice:** Rotate secret setiap 90-180 hari (opsional)

---

## 🎯 Summary

```
✅ Secret ditampilkan SEKALI saat create
✅ 1 Client ID = BANYAK Redirect URI (recommended!)
✅ Redirect URI harus exact match (security)
✅ Client ID permanent, tidak bisa diubah
✅ HTTPS wajib untuk production
✅ Secret di-hash, tidak bisa di-retrieve
✅ Multiple URI untuk dev/staging/prod
✅ Secret berlaku selamanya (sampai revoke/regenerate)
```

---

## 🔗 Related

-   [OAUTH_CLIENT_GUIDE.md](./OAUTH_CLIENT_GUIDE.md) - Panduan lengkap OAuth
-   [OAUTH_CLIENT_MANAGEMENT_COMPLETE.md](./OAUTH_CLIENT_MANAGEMENT_COMPLETE.md) - Feature summary

---

💡 **Pro Tip:** Selalu save Client ID & Secret di password manager atau secret vault, JANGAN di commit ke Git!
