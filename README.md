# Take My Lease Over

Property lease management system built with PHP.

## Quick Start

For development setup and local installation, see the main documentation.

## Deployment to cPanel

This project is configured for easy deployment to cPanel hosting using Git.

### Quick Deployment Steps

1. **Add cPanel remote** (one-time setup):
   
   Option A - Use the helper script:
   ```bash
   ./setup-cpanel-remote.sh
   ```
   
   Option B - Manual setup:
   ```bash
   git remote add cpanel ssh://username@yourdomain.com/home/username/repositories/takemyleseover
   ```

2. **Deploy changes**:
   ```bash
   git push cpanel main
   ```

For detailed deployment instructions, see [DEPLOY.md](DEPLOY.md).

## Requirements

- PHP 8.0 or higher
- MySQL database
- cPanel hosting with Git Version Control feature

## Configuration Files

- `.cpanel.yml` - Automated deployment configuration
- `.gitignore` - Files excluded from version control
- `config.php` - Database and application configuration

## License

Proprietary
