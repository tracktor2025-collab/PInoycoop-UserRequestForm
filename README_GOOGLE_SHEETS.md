# Google Sheets Integration (Laravel)

This project uses `revolution/laravel-google-sheets` to append rows into a Google Sheets spreadsheet when users submit the form.

## How it works (in this repo)
- The integration lives in `app/Http/Controllers/UserAccessRequestController.php`.
- When a submission happens, the controller:
  1. Reads your spreadsheet ID from `config('google.spreadsheet_id')` / `env('GOOGLE_SPREADSHEET_ID')`.
  2. Chooses a Google Sheets *tab (worksheet)* based on the selected systems and your `config('google.sheet_by_system')` mapping.
  3. Ensures the tab exists (creates it if missing).
  4. Ensures/updates the header row (row 1) to match what the controller expects.
  5. Appends the new row to the sheet.

## Step 1: Create Google Cloud credentials
1. Go to the [Google Cloud Console](https://console.cloud.google.com/).
2. Create (or select) a project.
3. Enable these APIs:
   - Google Sheets API
   - Google Drive API
4. Create a **Service Account**:
   - Name it (example: `laravel-sheets-writer`)
   - Grant it access to what you need (you will also share the spreadsheet in Step 3)
5. Create a key for the service account:
   - Choose **JSON**
   - Download the JSON file (keep it private)

## Step 2: Put the service account JSON in a safe location
Choose where to store the downloaded JSON on your server, then set:

`GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION`

This project’s `config/google.php` supports:
- Windows absolute paths (example: `C:\xampp2\htdocs\Web_UserRequest\storage\google\service-account.json`)
- Relative paths (relative to your Laravel `base_path`) (example: `storage/google/service-account.json`)

## Step 3: Share the spreadsheet with the service account
1. Open your target Google Spreadsheet in the browser.
2. Click **Share**
3. Add the service account email (found in the JSON, usually `client_email`)
4. Grant access like **Editor** (or at least permissions that allow writing/appending)

## Step 4: Configure environment variables
Update your `.env` file (there is no Google-related section in `.env.example` in this repo, so you must add them yourself).

At minimum, set:
```env
# Enable service-account based auth
GOOGLE_SERVICE_ENABLED=true

# Path to the service account JSON key
GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION="C:\PATH\TO\service-account.json"

# Your spreadsheet id (the long id in the URL)
GOOGLE_SPREADSHEET_ID="YOUR_SPREADSHEET_ID"

# Default tab name used when no mapping matches
GOOGLE_SHEET_NAME="Sheet1"

# Fallback tab for "other systems" (optional but recommended)
GOOGLE_SHEET_OTHER="Other Systems"
```

Then set tab mapping for these systems (these keys match `config('google.sheet_by_system')`):
```env
GOOGLE_SHEET_ATM="ATM Portal"
GOOGLE_SHEET_SMS="SMS Portal"
GOOGLE_SHEET_MSP="MSP-ISS Portal"
GOOGLE_SHEET_FTP="MSP-ISS FTP"
GOOGLE_SHEET_CORE="CORE 3.0"
GOOGLE_SHEET_MVM="MVM Portal"
```

### Important: system names must match
In the form (`resources/views/user-request-form.blade.php`), the checkboxes use specific system names (for example: `ATM Portal`, `CORE 3.0`, `MSP-ISS Portal`, etc.).

The controller uses those exact names to choose which tab to write into.

If a selected system is not in `sheet_by_system`, the controller writes the row into `GOOGLE_SHEET_OTHER`.

## Step 5: Create/prepare the Google Sheets tabs (worksheet titles)
Create tabs whose **tab names exactly match** the environment mapping values, for example:
- Tab `ATM Portal`
- Tab `CORE 3.0`
- Tab `MSP-ISS FTP`
- Tab `Other Systems`

The app can create missing tabs automatically, but the title must match what your env variables specify.

## Step 6: Restart Laravel (apply env changes)
After updating `.env`:
```bash
php artisan config:clear
```
Then restart your server (if you have it running).

## Step 7: Verify by submitting the form
Run your app and submit the user request:
- It should append a row to the proper tab(s).
- The first row (header) will be created/updated if it doesn’t match the controller’s expected header.

## Expected columns (headers)
The controller builds headers dynamically per selected system and appends one row per matched *tab*.

### Common columns (used by most systems)
These are always included:
- `Timestamp`
- `Request Type`
- `Resource Request Number`
- `Full Name`
- `Request Date`
- `Mobile No`
- `Coop Name Branch`
- `Postal Code`
- `Gender`
- `Address`
- `Email Address`
- `Place Of Birth`
- `Systems Requested`
- `Access Type`
- `Access End Date`
- `Job Title`

### System-specific extra columns
Your sheet tab will include the following extra columns depending on which system is treated as the “main” system for that tab:
- `CORE 3.0` adds: `Core Roles`
- `ATM Portal` adds: `ATM Access`
- `MVM Portal` adds: `MVM Roles`
- `MSP-ISS Portal` adds:
  - `MSP Coop Code`
  - `MSP Username`
  - `MSP Submission Type`
  - `MSP End Date`
  - `Provider Code (CIC)`
  - `Password (CIC)`
  - `User Role`
- `MSP-ISS FTP` adds:
  - `FTP Allowed`
  - `Provider Code (CIC)`
  - `Password (CIC)`
  - `FTP Roles`
- Other/unmapped systems use only the Common columns.

## Troubleshooting
- “Insufficient permissions” / “Access denied”
  - Confirm you shared the spreadsheet with the **service account email** and granted write access.
- “Invalid credentials” / “Could not read service account”
  - Confirm `GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION` path is correct (and accessible by PHP).
- Rows are appended to the wrong tab
  - Check that your `GOOGLE_SHEET_*` values match the **tab titles** and match the system names used by the form.
- Header keeps changing
  - The controller compares and updates row 1 if it differs from its expected header. Keep headers aligned with the project’s expected columns.

