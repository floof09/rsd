# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

## Deployment to Hostinger (GitHub Actions)

This project includes two GitHub Actions workflows to deploy to Hostinger on pushes to the `main` branch:

- `.github/workflows/deploy-hostinger.yml` – Deploy via FTP
- `.github/workflows/deploy-ssh.yml` – Deploy via SSH/rsync (faster, supports post-deploy commands)

### 1) Prepare Hostinger

- Create subdomain: `rsdlearninghub.rsdhrmc.com`
- Set document root to: `/public_html/rsd/public` (the CI4 `public/` directory)
- Ensure PHP 8.2+ is active for the domain
- Create the MySQL database and user; note credentials
- Install SSL (Let’s Encrypt)

### 2) GitHub Secrets

Choose one deployment method and set these repo secrets:

FTP method (`deploy-hostinger.yml`):
- `HOSTINGER_FTP_HOST` – FTP hostname
- `HOSTINGER_FTP_USER` – FTP username
- `HOSTINGER_FTP_PASS` – FTP password
- `HOSTINGER_FTP_DIR` – Remote target directory (e.g. `/public_html/rsd/`)

SSH/rsync method (`deploy-ssh.yml`):
- `SSH_HOST` – SSH hostname
- `SSH_USER` – SSH username
- `SSH_PORT` – SSH port (e.g. `65002`)
- `SSH_PRIVATE_KEY` – Private key contents (PEM) for the user
- `SSH_TARGET_DIR` – Remote target directory (e.g. `/home/<user>/public_html/rsd/`)

Optional for both:
- `ENV_PROD` – Full contents of your production `.env` file

> Note: The SSH workflow respects `.rsyncignore` to avoid uploading dev/test files.

### 3) Configure environment

Create and tailor `.env` (or provide via `ENV_PROD` secret). Minimum settings:

- `app.baseURL = 'https://rsdlearninghub.rsdhrmc.com/'`
- Database credentials under `database.*`

Writable directories (`writable/`) must be writable by PHP on the server.

### 4) Deploy

- Push to `main` or manually run the chosen workflow in the Actions tab
- For SSH, the workflow can run post-deploy commands like `php spark cache:clear`

### 5) Troubleshooting

- If the site shows a directory listing, the document root is wrong; point to `/public_html/rsd/public`
- Check `writable/logs/` on the server for errors
- If CSS/JS look cached, clear cache (`php spark cache:clear`) and hard-refresh the browser

## Hostinger Auto Deployment (Webhook)

If you prefer Hostinger's built-in auto-deployment instead of GitHub Actions:

1. In hPanel → Websites → Advanced → Git → your repo, enable Auto Deployment and copy the Webhook URL.
2. In GitHub → Settings → Webhooks → Add webhook:
	- Payload URL: paste the Hostinger Webhook URL
	- Content type: `application/json`
	- Secret: leave blank
	- Events: "Just the push event"
3. Save, then push any commit to `main` to trigger deployment.

This paragraph intentionally added to trigger the initial webhook test. testestestsetset
