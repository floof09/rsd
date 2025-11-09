# 🧪 LOCAL DEVELOPMENT → PRODUCTION WORKFLOW

## 🎯 Goal
Test changes locally BEFORE pushing to production to protect live client data.

---

## 📋 Initial Setup (One Time Only)

### 1. Export Production Database
1. Log into Hostinger cPanel → phpMyAdmin
2. Select database `u911935302_WTXcQ`
3. Click **Export** → **Quick** → **SQL** → **Go**
4. Save as `production_backup_YYYYMMDD.sql`

### 2. Import to Local Database
1. Open `http://localhost/phpmyadmin/`
2. Create database: `rsd_local`
3. Select `rsd_local` → **Import** → Choose the SQL file
4. Wait for import to complete

### 3. Switch to Local Environment
```powershell
# In c:\xampp\htdocs\rsd\
Copy-Item .env.development .env -Force
```

### 4. Start XAMPP
- Start Apache and MySQL in XAMPP Control Panel
- Visit: `http://localhost/rsd/`

---

## 🔄 Daily Development Workflow

### STEP 1: Work Locally
```powershell
# Ensure you're using LOCAL database
Copy-Item .env.development .env -Force

# Start XAMPP services
# Work on your changes
# Test at http://localhost/rsd/
```

### STEP 2: Test Your Changes
- ✅ Test login/logout
- ✅ Test forms (applications, user management)
- ✅ Test dashboard rendering
- ✅ Test sidebar collapse/expand
- ✅ Test responsive design (resize browser)
- ✅ Check browser console for errors (F12)
- ✅ Test on different browsers (Chrome, Firefox, Edge)

### STEP 3: Commit to Git (ONLY if tests pass)
```powershell
# Check what changed
git status
git diff

# Stage ONLY the files you want to push
git add public/assets/css/sidebar.css
git add public/assets/css/dashboard.css
# etc.

# Commit with descriptive message
git commit -m "Fix: Centered dashboard content and increased layout width"

# Push to GitHub
git push origin main
```

### STEP 4: Deploy to Production
```powershell
# SSH into Hostinger OR use their Git Deployment feature
# Pull latest changes
git pull origin main

# Make sure production .env is active
# (Should already be there, but verify)
```

### STEP 5: Verify on Production
- Visit `https://rsdlearninghub.rsdhrmc.com/`
- Test the same features you tested locally
- If something breaks, you can quickly revert:
  ```bash
  git revert HEAD
  git push origin main
  ```

---

## 🚨 CRITICAL RULES

### ❌ NEVER:
- Work directly on production files via cPanel File Manager
- Test on production with live client data
- Commit code you haven't tested locally
- Push database changes without backups
- Use production credentials in local .env

### ✅ ALWAYS:
- Test locally first with local database
- Commit small, logical changes (not 50 files at once)
- Write clear commit messages
- Keep backups of production database
- Use `.env.development` locally, `.env.production` on server

---

## 📁 Files to NEVER Commit to Git

Add these to `.gitignore`:
```
.env
.env.production
writable/logs/*
writable/cache/*
writable/session/*
writable/debugbar/*
writable/uploads/*
vendor/
```

---

## 🔧 Quick Reference Commands

### Switch to Local Development
```powershell
Copy-Item .env.development .env -Force
```

### Check Current Environment
```powershell
Select-String -Pattern "CI_ENVIRONMENT" .env
```

### Create Database Backup (Local)
```powershell
# From XAMPP MySQL bin directory:
cd C:\xampp\mysql\bin
.\mysqldump.exe -u root rsd_local > "C:\xampp\htdocs\rsd\backups\local_backup_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql"
```

### View Git Changes Before Commit
```powershell
git status           # See modified files
git diff             # See line-by-line changes
git diff --staged    # See changes staged for commit
```

### Undo Local Changes (if you mess up)
```powershell
git checkout -- filename.css    # Undo changes to one file
git reset --hard                # Undo ALL uncommitted changes (DANGEROUS!)
```

---

## 🎓 Best Practices

1. **Small Commits**: One feature/fix per commit
2. **Test Everything**: Don't assume it works
3. **Descriptive Messages**: "Fix sidebar width" not "update css"
4. **Branch Protection**: Consider creating a `dev` branch for experiments
5. **Regular Backups**: Export production DB weekly
6. **Document Changes**: Keep notes on what you changed and why

---

## 📞 Emergency Rollback

If production breaks after deployment:

```bash
# SSH into Hostinger
git log --oneline           # See recent commits
git revert HEAD             # Undo last commit
git push origin main        # Push the revert

# OR restore from backup
# Import previous database backup in cPanel phpMyAdmin
```

---

## ✅ Pre-Deployment Checklist

Before `git push`:

- [ ] Tested locally with local database
- [ ] All forms work correctly
- [ ] No console errors in browser (F12)
- [ ] Responsive design looks good
- [ ] Hard refresh tested (Ctrl+Shift+R)
- [ ] Git diff reviewed (no accidental changes)
- [ ] Commit message is descriptive
- [ ] `.env` file is NOT staged for commit
- [ ] Production backup exists (just in case)

Now you're ready to push! 🚀
