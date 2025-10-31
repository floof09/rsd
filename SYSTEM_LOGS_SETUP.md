# System Logs Feature - Setup & Testing Guide

## Overview
A comprehensive system activity logging feature has been implemented to track all user actions in the RSD application. This includes authentication events, application creation, and other system activities.

## What Has Been Implemented

### 1. Database Migration
**File:** `app/Database/Migrations/2025-10-28-213802_CreateSystemLogsTable.php`

Creates the `system_logs` table with the following structure:
- `id` - Primary key
- `user_id` - Foreign key to users table (nullable for anonymous actions)
- `action` - What action was performed (e.g., "Login", "Created Application")
- `module` - Category of action (auth, application, user, system)
- `description` - Detailed description of the action
- `ip_address` - IP address of the user (supports IPv6)
- `user_agent` - Browser/client information
- `created_at` - Timestamp of the action

**Indexes created for performance:**
- `user_id`
- `module`
- `created_at`

### 2. SystemLogModel
**File:** `app/Models/SystemLogModel.php`

**Key Methods:**
- `logActivity($action, $module, $description, $userId)` - Records a new log entry
  - Automatically captures IP address and user agent
  - Sanitizes data before insertion
  
- `getLogsWithUsers($limit, $offset)` - Retrieves logs with user information
  - Joins with users table
  - Returns user email and name with each log
  
- `getLogsByModule($module, $limit)` - Filter logs by module type
- `getLogsByUser($userId, $limit)` - Get all logs for a specific user
- `getRecentActivity($limit)` - Get the most recent log entries

### 3. SystemLogs Controller
**File:** `app/Controllers/SystemLogs.php`

**Routes:**
- `GET /admin/system-logs` - View all system logs
- `GET /admin/system-logs/filter/:module` - Filter by module (auth, application, user, system)
- `POST /admin/system-logs/clear-old` - Delete logs older than 30 days

**Features:**
- Admin-only access
- Pagination (100 logs per page)
- Module filtering
- Bulk deletion of old logs

### 4. System Logs View
**File:** `app/Views/admin/system_logs.php`

**UI Features:**
- Stats header showing total log count
- Module filter dropdown
- Export to CSV functionality
- Clear old logs button
- Responsive table with columns:
  - Time (formatted as HH:mm:ss with date)
  - User (with avatar and email)
  - Module (color-coded badge)
  - Action
  - Description
  - IP Address
- Empty state message when no logs exist

**Color-coded module badges:**
- 🔐 Auth (Blue) - Login, logout, failed login attempts
- 📝 Application (Green) - Application creation, updates
- 👤 User (Yellow) - User management actions
- ⚙️ System (Purple) - System-level events

### 5. CSS Styling
**File:** `assets/css/system-logs.css`

Professional styling with:
- Glass morphism effects
- Hover animations
- Responsive design
- Color-coded module badges
- User avatar displays
- Clean table layout

### 6. Integration Points

#### Auth Controller (`app/Controllers/Auth.php`)
**Logging added for:**
- ✅ Successful login
- ✅ Failed login attempts (with email)
- ✅ Inactive account login attempts
- ✅ User logout

#### AdminApplication Controller (`app/Controllers/AdminApplication.php`)
**Logging added for:**
- ✅ Application creation (with applicant name and ID)

#### AdminDashboard Controller (`app/Controllers/AdminDashboard.php`)
**Updated to:**
- ✅ Fetch recent system logs
- ✅ Display in dashboard widget

### 7. Dashboard Widget
**File:** `app/Views/admin/dashboard.php`

Added "System Activity" section showing:
- Last 5 system activities
- Icons based on module type
- User information (when available)
- Time ago format
- Link to view all logs

### 8. Sidebar Menu
**File:** `app/Views/components/admin_sidebar.php`

Added:
- ✅ "System Logs" menu item with document icon
- ✅ Active state highlighting

---

## Setup Instructions

### Step 1: Start MySQL Service

**Option A: Via XAMPP Control Panel**
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait for the service to turn green

**Option B: Via Command Line**
```bash
# Navigate to XAMPP installation
cd C:\xampp
mysql_start.bat
```

### Step 2: Run Database Migration

Once MySQL is running, execute:

```bash
php spark migrate
```

**Expected Output:**
```
Running all new migrations...
Migrating: 2025-10-28-213802_CreateSystemLogsTable
Done running: 2025-10-28-213802_CreateSystemLogsTable
```

### Step 3: Verify Table Creation

Check if the table was created:

```bash
php spark db:table system_logs
```

Or via MySQL CLI:
```sql
USE ci4;
DESCRIBE system_logs;
```

### Step 4: Test the Feature

1. **Test Login Logging:**
   - Go to http://localhost/rsd/auth/login
   - Log in with admin credentials
   - Check system logs at http://localhost/rsd/admin/system-logs
   - You should see a "Login" entry

2. **Test Failed Login:**
   - Try logging in with wrong credentials
   - Check logs for "Failed Login" entry

3. **Test Application Creation:**
   - Create a new application
   - Check logs for "Created Application" entry

4. **Test Dashboard Widget:**
   - Go to http://localhost/rsd/admin/dashboard
   - Scroll down to see "System Activity" section
   - Should show recent 5 activities

5. **Test Filtering:**
   - Go to system logs page
   - Use the module dropdown to filter by "Auth", "Application", etc.

6. **Test CSV Export:**
   - Click "Export to CSV" button
   - Verify CSV file downloads with log data

7. **Test Clear Old Logs:**
   - Click "Clear Old Logs" button
   - Logs older than 30 days should be deleted

---

## Module Types & Usage

### Auth Module
**Used for:** Authentication and authorization events
```php
$systemLog->logActivity(
    'Login',
    'auth',
    'User logged in successfully',
    $userId
);
```

### Application Module
**Used for:** Application-related actions
```php
$systemLog->logActivity(
    'Created Application',
    'application',
    'Created application for John Doe (ID: 123)',
    $userId
);
```

### User Module
**Used for:** User management actions (future)
```php
$systemLog->logActivity(
    'Updated User',
    'user',
    'Updated profile for user ID: 5',
    $adminUserId
);
```

### System Module
**Used for:** System-level events (future)
```php
$systemLog->logActivity(
    'Database Backup',
    'system',
    'Automated database backup completed',
    null
);
```

---

## How to Add Logging to Other Controllers

### Example 1: Basic Logging
```php
use App\Models\SystemLogModel;

class YourController extends BaseController
{
    public function yourMethod()
    {
        // Your code here
        
        // Log the action
        $systemLog = new SystemLogModel();
        $systemLog->logActivity(
            'Action Name',
            'module_type',
            'Detailed description of what happened',
            session()->get('user_id')
        );
    }
}
```

### Example 2: Logging Without User (Anonymous)
```php
$systemLog->logActivity(
    'Public Form Submission',
    'system',
    'Public inquiry form submitted',
    null  // No user ID for anonymous actions
);
```

### Example 3: Logging with Dynamic Data
```php
$applicationId = $applicationModel->getInsertID();
$systemLog->logActivity(
    'Approved Application',
    'application',
    "Approved application ID: {$applicationId} for {$applicantName}",
    session()->get('user_id')
);
```

---

## Advanced Features (Future Enhancements)

### 1. Log Levels
Add severity levels to logs:
```php
$systemLog->logActivity(
    'Database Error',
    'system',
    'Connection timeout to database',
    null,
    'ERROR'  // Add level parameter
);
```

### 2. Search Functionality
Search logs by:
- Action name
- Description content
- Date range
- User email

### 3. Real-time Updates
Implement WebSockets or polling to show live logs:
```javascript
setInterval(() => {
    fetch('/admin/system-logs/latest')
        .then(response => response.json())
        .then(data => updateLogsTable(data));
}, 5000);
```

### 4. Log Analysis Dashboard
Create visualizations:
- Most active users chart
- Peak activity times graph
- Failed login attempts over time
- Module usage pie chart

### 5. Email Notifications
Send alerts for critical events:
```php
if ($failedLoginCount > 5) {
    $systemLog->logActivity(
        'Security Alert',
        'auth',
        "Multiple failed login attempts for {$email}",
        null
    );
    // Send email to admin
    sendSecurityAlert($email, $failedLoginCount);
}
```

### 6. Log Retention Policies
Implement automatic archival:
- Keep detailed logs for 30 days
- Archive logs 31-90 days old
- Keep summary logs for 1 year
- Delete logs older than 1 year

---

## Troubleshooting

### Issue: Migration Fails
**Error:** "Unable to connect to the database"
**Solution:** 
1. Check if MySQL is running in XAMPP
2. Verify database credentials in `.env` file
3. Ensure `ci4` database exists

### Issue: Logs Not Appearing
**Possible Causes:**
1. Migration not run (table doesn't exist)
2. Controller not including SystemLogModel
3. Code not calling logActivity() method

**Check:**
```bash
# Verify table exists
php spark db:table system_logs

# Check for errors in logs
tail -f writable/logs/log-*.log
```

### Issue: "Permission Denied" Error
**Solution:**
```bash
# Fix permissions on writable directory
chmod -R 777 writable/
```

### Issue: CSV Export Not Working
**Check:**
- Browser console for JavaScript errors
- Ensure logs table has data
- Check if JavaScript is enabled

---

## Performance Considerations

### Indexing
The following indexes are created for optimal performance:
- `user_id` - Fast user-based queries
- `module` - Quick module filtering
- `created_at` - Efficient time-based queries

### Log Rotation
Implement periodic cleanup:
```php
// In a scheduled task (cron job)
$systemLog->where('created_at <', date('Y-m-d', strtotime('-30 days')))
           ->delete();
```

### Query Optimization
Use pagination to avoid loading all logs:
```php
$logs = $systemLog->getLogsWithUsers(100, $offset);
```

---

## Security Notes

### Data Privacy
- IP addresses are logged for security purposes
- User agents can contain system information
- Consider GDPR implications for EU users
- Implement data retention policies

### Access Control
- System logs are admin-only
- Ensure proper session validation
- Don't expose sensitive data in descriptions

### Audit Trail Integrity
- Logs should never be editable (append-only)
- Consider adding checksum/hash for tamper detection
- Implement log signing for compliance

---

## Testing Checklist

- [ ] MySQL service is running
- [ ] Migration executed successfully
- [ ] System logs table exists
- [ ] Login creates log entry
- [ ] Failed login creates log entry
- [ ] Logout creates log entry
- [ ] Application creation creates log entry
- [ ] Dashboard shows system activity widget
- [ ] System logs page loads correctly
- [ ] Module filtering works
- [ ] CSV export downloads
- [ ] Clear old logs button works
- [ ] Empty state displays when no logs
- [ ] Responsive design works on mobile
- [ ] User avatars display correctly
- [ ] Module badges are color-coded
- [ ] Times display in "time ago" format

---

## File Checklist

All these files have been created/modified:

### New Files Created:
- ✅ `app/Database/Migrations/2025-10-28-213802_CreateSystemLogsTable.php`
- ✅ `app/Models/SystemLogModel.php`
- ✅ `app/Controllers/SystemLogs.php`
- ✅ `app/Views/admin/system_logs.php`
- ✅ `assets/css/system-logs.css`

### Modified Files:
- ✅ `app/Controllers/Auth.php` - Added logging for authentication
- ✅ `app/Controllers/AdminApplication.php` - Added logging for applications
- ✅ `app/Controllers/AdminDashboard.php` - Added recent logs fetch
- ✅ `app/Views/admin/dashboard.php` - Added system activity widget
- ✅ `app/Views/components/admin_sidebar.php` - Added System Logs menu item
- ✅ `app/Config/Routes.php` - Added system logs routes

---

## Current Status

### ✅ Completed
- Database migration file created
- SystemLogModel with all methods
- SystemLogs controller with view/filter/clear
- System logs view with responsive UI
- CSS styling complete
- Routes configured
- Sidebar menu updated
- Auth controller integrated with logging
- AdminApplication controller integrated
- Dashboard widget added

### ⏳ Pending (Blocked by MySQL)
- Run migration to create table
- Test all functionality
- Verify logs are being created

### 🔄 Future Enhancements
- Add logging to more controllers
- Implement search functionality
- Add date range filtering
- Create log analysis dashboard
- Add email notifications for critical events
- Implement log retention policies
- Add export to PDF
- Create scheduled reports

---

## Support & Documentation

For more information on CodeIgniter migrations:
```bash
php spark migrate --help
```

For database information:
```bash
php spark db:table system_logs
```

To rollback migration (if needed):
```bash
php spark migrate:rollback
```

---

**Created:** October 28, 2025
**Version:** 1.0
**Status:** Ready for Testing (pending MySQL connection)
