# Git Auto-Push untuk VPS Deployment

## 📋 Overview

Dokumentasi ini menjelaskan cara setup otomasi git push dari local development ke GitHub, sehingga perubahan otomatis ter-push dan dapat di-pull di VPS dengan `git pull origin main`.

**Repository:** https://github.com/raygums/Domain-TIK.git  
**Branch:** `main` (default untuk production)  
**Current Remotes:**
```
remote.origin.url=https://github.com/raygums/Domain-TIK.git
```

---

## 🔧 Opsi 1: Manual Push dengan Script Batch (Windows)

### Setup

Sudah ada script di: `auto-push.bat`

### Cara Menggunakan

1. **Direct Execution:**
   ```powershell
   c:\laragon\www\Domain-TIK\auto-push.bat
   ```

2. **Membuat Shortcut di Desktop:**
   - Klik kanan di desktop → New → Shortcut
   - Target: `c:\laragon\www\Domain-TIK\auto-push.bat`
   - Start in: `c:\laragon\www\Domain-TIK`
   - Name: "Push Domain-TIK to Git"
   - Klik icon → Properties → Advanced → Check "Run as administrator"

3. **Jadwalkan dengan Windows Task Scheduler:**
   - Buka Task Scheduler (ketik `task scheduler` di Windows search)
   - Create Basic Task
   - Name: "Domain-TIK Auto Push"
   - Trigger: Daily at specific time (misal: 6 PM)
   - Action: Start a program
   - Program: `C:\Windows\System32\cmd.exe`
   - Arguments: `/c c:\laragon\www\Domain-TIK\auto-push.bat`
   - Conditions: Only if user is logged on

---

## 🔧 Opsi 2: Git Post-Commit Hook (Automatic)

Ini akan push otomatis setiap kali Anda commit (hanya di local machine).

### Setup

1. **Create hook file:**
   ```powershell
   # Buka file: .git/hooks/post-commit
   # Tambahkan isi:
   ```

2. **File: `.git/hooks/post-commit` (buat file baru, NO extension):**
   ```bash
   #!/bin/bash
   # Auto-push after commit
   
   BRANCH=$(git rev-parse --abbrev-ref HEAD)
   
   # Hanya push jika branch adalah main
   if [ "$BRANCH" = "main" ]; then
       echo "[HOOK] Auto-pushing to origin main..."
       git push origin main
       
       if [ $? -eq 0 ]; then
           echo "[HOOK] ✓ Push successful"
       else
           echo "[HOOK] ✗ Push failed - check connection"
       fi
   fi
   ```

3. **Make executable (di Git Bash atau Terminal):**
   ```bash
   chmod +x .git/hooks/post-commit
   ```

4. **Test:**
   ```bash
   # Buat perubahan apapun
   git add .
   git commit -m "Test auto-push"
   # Seharusnya akan push otomatis
   ```

---

## 🔧 Opsi 3: GitHub Actions (Recommended untuk Team)

Jika ingin VPS automatically deploy saat push ke GitHub:

### File: `.github/workflows/deploy-to-vps.yml`

```yaml
name: Deploy to VPS

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Deploy via SSH
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.VPS_HOST }}
        username: ${{ secrets.VPS_USER }}
        key: ${{ secrets.VPS_SSH_KEY }}
        port: 22
        script: |
          cd /path/to/app
          git pull origin main
          composer install
          php artisan migrate
          php artisan cache:clear
```

### Setup GitHub Secrets:

1. Go: https://github.com/raygums/Domain-TIK/settings/secrets/actions
2. Add:
   - `VPS_HOST`: IP atau domain VPS Anda
   - `VPS_USER`: SSH user (usually `root` atau `deploy`)
   - `VPS_SSH_KEY`: Private SSH key dari VPS

---

## 📤 Current Git Status

```
On branch: main
Remote: origin (https://github.com/raygums/Domain-TIK.git)
Untracked files:
  - app/Http/Controllers/Admin/NotificationController.php
  - app/Http/Controllers/NotificationController.php
  - app/Models/AdminNotification.php
  - app/Services/NotificationService.php
  - database/migrations/2026_05_04_*.php
  - resources/views/admin/notifications/
  - resources/views/user/notifications/
  - resources/views/emails/
  - routes/web.php (modified)
```

---

## ✅ Testing Checklist

Sebelum deploy ke VPS:

- [ ] Verify `.env` credentials (especially DB connection)
- [ ] Run `php artisan migrate` to create tables
- [ ] Test notification system locally
- [ ] Verify git push works: `git push origin main`
- [ ] Verify git pull di VPS: `cd /path/to/app && git pull origin main`
- [ ] Check git log pada kedua machines

---

## 🚀 Deployment Steps (di VPS)

Setelah menggunakan auto-push:

```bash
# SSH ke VPS
ssh user@vps_ip

# Navigate ke app directory
cd /path/to/Domain-TIK

# Pull latest changes
git pull origin main

# Update dependencies (jika ada changes di composer.json)
composer install

# Run pending migrations
php artisan migrate

# Clear caches
php artisan cache:clear
php artisan config:cache

# Restart queue workers (jika ada)
php artisan queue:restart

# Done!
```

---

## 🔐 Authentication Notes

- **GitHub HTTPS:** Jika menggunakan HTTPS, GitHub meminta Personal Access Token (tidak bisa password)
  1. Generate token: https://github.com/settings/tokens
  2. Scopes: `repo`, `workflow`
  3. Update credentials: `git config --global credential.helper store`
  4. Next push akan prompt untuk token

- **GitHub SSH:** Lebih aman untuk automation
  1. Generate SSH key: `ssh-keygen -t ed25519`
  2. Add public key ke GitHub: https://github.com/settings/keys
  3. Update remote: `git remote set-url origin git@github.com:raygums/Domain-TIK.git`

---

## 📝 Next Steps

1. **Immediate:**
   - [ ] Commit current changes: `git add -A && git commit -m "Add notification system + routes + git automation"`
   - [ ] Push to GitHub: `auto-push.bat` atau `git push origin main`

2. **VPS Setup:**
   - [ ] SSH ke VPS
   - [ ] `git pull origin main`
   - [ ] `php artisan migrate`
   - [ ] Test notification pages accessible

3. **Automation Choice:**
   - Choose ONE method:
     - **Manual + Task Scheduler** (simple, predictable)
     - **Post-commit hook** (instant, automatic)
     - **GitHub Actions** (advanced, for CI/CD pipeline)

---

## 📞 Troubleshooting

**Push fails: "Permission denied"**
- Solution: Update SSH key atau use HTTPS with PAT token

**Changes not pushing**
- Check: `git status` ada uncommitted changes?
- Check: Branch adalah `main`?
- Check: Internet connection aktif?

**VPS git pull gagal**
- Check: VPS punya git installed? `git --version`
- Check: Remote configured? `git remote -v`
- Check: Branch exists? `git branch -a`

---

## 📚 References

- Git Hooks: https://git-scm.com/book/en/v2/Customizing-Git-Git-Hooks
- GitHub Actions: https://docs.github.com/en/actions
- SSH Keys: https://docs.github.com/en/authentication/connecting-to-github-with-ssh
