# Deploying the site with Supabase (PostgreSQL) + Render

The site now talks to PostgreSQL through PDO, so it works with Supabase.

## 1. Create the database in Supabase

1. Open your Supabase project, then go to **SQL Editor** and click **New query**.
2. Open `database/supabase-setup.sql` from this repo, copy everything, paste it in, and click **Run**.
   - It creates all 12 tables and loads the data from the old `ngo_db (2).sql`.
   - It drops the tables first, so any half-made tables from earlier attempts are replaced.
     Don't re-run it once real data is coming in, or that data will be wiped.
3. Check **Table Editor**: you should see `events` (3 rows), `leaders` (4), `modules` (8), `users` (2) and so on.

## 2. Copy the right connection string

1. In Supabase, click **Connect** (top of the project page).
2. Choose **Session pooler**. Do NOT use "Direct connection": that address only works over IPv6, and Render can't reach it.
3. Copy the URI. It looks like:

   ```
   postgresql://postgres.abcdefghijkl:[YOUR-PASSWORD]@aws-0-ap-southeast-1.pooler.supabase.com:5432/postgres
   ```

4. Replace `[YOUR-PASSWORD]` with your database password (no brackets).
   If the password has symbols like `@ # / ? % :` either URL-encode them or reset the password
   (Project Settings > Database > Reset database password) to one with only letters and numbers.

## 3. Point Render at Supabase

1. Render dashboard > your web service > **Environment**.
2. Add (or edit) `DATABASE_URL` and paste the Session pooler URI from step 2.
3. Delete the old variables if they are still there: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`, `DB_PORT`.
4. Remove the expired Render PostgreSQL database from the service's linked resources (it is no longer used).
5. Click **Save, rebuild, and deploy** (or Manual Deploy > Deploy latest commit).

The Dockerfile now installs `pdo_pgsql`, so the rebuild is required.

## 4. Check it

- Home, About, Events and News pages should show the old content.
- Log in at `/login.php` with your admin account and open the admin dashboard.
- If you see "Database connection failed", the `DATABASE_URL` value is wrong. Check the Render logs for the exact reason.

## Good to know

- Render's free plan does not keep files between deploys. Anything uploaded through the site
  (receipts, leader photos, news images, module PDFs, student submissions) disappears on the next
  deploy or restart. Files committed to this repo are fine. To keep uploads permanently, they would
  need to go to Supabase Storage.
- For local testing, set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` (and `DB_SSLMODE=disable`
  if your local PostgreSQL has no SSL) instead of `DATABASE_URL`.
