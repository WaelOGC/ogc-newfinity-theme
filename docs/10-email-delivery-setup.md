# 10 — Email Delivery Setup (Contact Form)

## Why messages may not arrive

The Contact form uses WordPress `wp_mail()` correctly. Delivery is a **server/transport** issue, not a theme bug.

By default, `wp_mail()` calls PHP’s `mail()`. On most hosts (including Hostinger) that path often:

- Is silently dropped or spam-filtered without proper SPF/DKIM for the sending domain, or
- Does not work at all on typical **local** WordPress installs (no local MTA / mail server).

So a “Thanks — your message is on its way” notice only means the theme accepted the submission and handed it to WordPress — not that the mailbox received it.

## Fix (one-time WP admin setup — not a theme change)

1. In **WP Admin → Plugins**, install and activate **WP Mail SMTP** (by WPForms).
2. Configure it to send through the Hostinger mailbox **contact@ogcnewfinity.com** using Hostinger SMTP:
   - **Host:** `smtp.hostinger.com`
   - **Port:** `465` (SSL) or `587` (TLS)
   - **Username / password:** the real mailbox credentials for `contact@ogcnewfinity.com`
3. Send a test email from the plugin’s settings screen, then submit the site Contact form again.

Do **not** hardcode SMTP credentials into the theme. Keep them in the plugin (or environment) configuration only.

## Local vs live

Once WP Mail SMTP is configured, the same setup works on **localhost** and on the **live Hostinger server**, because SMTP goes out over the internet instead of relying on PHP `mail()` / a local mail server.

## Theme code

No change is required in `inc/contact-form.php` for delivery. After WP Mail SMTP is active and configured, `wp_mail()` continues to be used and sends through SMTP transparently.
