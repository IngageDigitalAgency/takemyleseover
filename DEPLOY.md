# cPanel Deployment Guide

This guide explains how to set up and use Git deployment with cPanel for the Take My Lease Over application.

## Prerequisites

- Access to cPanel hosting account
- SSH access to your cPanel server (recommended but not required)
- Git installed on your local machine
- cPanel Git Version Control feature enabled

## Initial Setup on cPanel

### 1. Create Git Repository in cPanel

1. Log into your cPanel account
2. Navigate to **Git™ Version Control** under the Files section
3. Click **Create** button
4. Configure the repository:
   - **Clone URL**: Leave empty (we'll push from local)
   - **Repository Path**: `public_html` (or your desired directory)
   - **Repository Name**: Enter a name (e.g., `takemyleseover`)
5. Click **Create** to create the repository

### 2. Note Your Repository Details

After creation, cPanel will display:
- Repository path on the server
- Clone URL (for reference)
- You'll need the SSH connection details for adding the remote

## Local Git Configuration

### 1. Add cPanel as a Remote Repository

**Option A: Use the Helper Script (Recommended)**

Run the provided setup script which will guide you through the configuration:

```bash
./setup-cpanel-remote.sh
```

The script will:
- Prompt you for cPanel connection details
- Configure the remote repository URL
- Provide next steps for deployment

**Option B: Manual Configuration**

In your local repository, add the cPanel server as a remote:

```bash
# SSH format (recommended)
git remote add cpanel ssh://username@yourdomain.com/home/username/repositories/takemyleseover

# Alternative format
git remote add cpanel username@yourdomain.com:~/repositories/takemyleseover
```

Replace:
- `username` with your cPanel username
- `yourdomain.com` with your domain or server IP
- `takemyleseover` with your repository name

### 2. Verify Remote Configuration

```bash
git remote -v
```

You should see both `origin` (GitHub) and `cpanel` remotes listed.

## Deployment Workflow

### Before First Deployment

1. **Update `.cpanel.yml` Configuration**
   
   Edit `.cpanel.yml` and replace `username` with your actual cPanel username:
   ```yaml
   - export DEPLOYPATH=/home/YOUR_CPANEL_USERNAME/public_html/
   ```

2. **Verify `.gitignore` Settings**
   
   Ensure sensitive files and directories are excluded:
   - Database credentials
   - Log files
   - Backup files
   - Development files

3. **Test Locally**
   
   Make sure your application works locally before deploying.

### Deploying Changes

#### Deploy to cPanel

```bash
# Make sure all changes are committed
git add .
git commit -m "Your commit message"

# Push to cPanel (this triggers automatic deployment)
git push cpanel main
```

Or if you're on a different branch:

```bash
git push cpanel your-branch-name:main
```

#### Deploy Specific Branch

```bash
# Push a specific branch
git push cpanel feature-branch:main

# Push current branch
git push cpanel HEAD:main
```

### Automatic Deployment Process

When you push to cPanel, the `.cpanel.yml` file automatically:

1. Creates the deployment directory if it doesn't exist
2. Syncs files from the repository to `public_html/`
3. Sets appropriate file permissions
4. Installs/updates Composer dependencies
5. Excludes `.git` directory and `.cpanel.yml` from deployment

## Common Tasks

### Update Deployment Path

Edit `.cpanel.yml` to change where files are deployed:

```yaml
- export DEPLOYPATH=/home/username/public_html/subdirectory/
```

### Manual Deployment Trigger

If auto-deployment doesn't work, you can manually trigger it in cPanel:

1. Go to **Git™ Version Control**
2. Click **Manage** on your repository
3. Click **Update from Remote** (if you have a remote configured)
4. Or click **Deploy HEAD Commit** to deploy the latest commit

### Check Deployment Logs

In cPanel Git Version Control:
1. Click **Manage** on your repository
2. Scroll down to view deployment logs
3. Check for any errors during deployment

## Troubleshooting

### Permission Denied Errors

If you get permission errors when pushing:

1. **Check SSH Key Authentication**
   ```bash
   # Generate SSH key if you don't have one
   ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
   
   # Copy your public key
   cat ~/.ssh/id_rsa.pub
   ```

2. **Add SSH Key to cPanel**
   - Log into cPanel
   - Go to **SSH Access** → **Manage SSH Keys**
   - Click **Import Key** and paste your public key
   - Authorize the key

3. **Test SSH Connection**
   ```bash
   ssh username@yourdomain.com
   ```

### Files Not Deploying

1. Check if files are tracked in Git:
   ```bash
   git status
   git ls-files
   ```

2. Verify `.gitignore` isn't excluding necessary files

3. Check deployment logs in cPanel for errors

### Database Configuration

The `config.php` file contains database credentials. Make sure:

1. It's tracked in Git (not in `.gitignore`)
2. Update database settings for production environment
3. Ensure file permissions are restrictive (600)

### Composer Dependencies

If composer install fails:

1. Check PHP version in `.cpanel.yml` (ea-php80, ea-php81, etc.)
2. Verify Composer is available on your cPanel server
3. Check composer.json for compatibility

## Security Best Practices

1. **Never commit sensitive data**
   - API keys
   - Passwords
   - Private keys

2. **Use environment variables** for sensitive configuration when possible

3. **Keep `.gitignore` updated** to exclude:
   - Log files
   - Cache files
   - User uploaded content
   - Backup files

4. **Regular backups** before deploying major changes:
   ```bash
   # In cPanel, use backup tools or create manual backup
   ```

5. **Test in staging** environment before production deployment

## Workflow Example

### Daily Development Workflow

```bash
# 1. Make changes locally
# Edit your files...

# 2. Test locally
php -S localhost:8000

# 3. Commit changes
git add .
git commit -m "Add new feature"

# 4. Push to GitHub (for backup and collaboration)
git push origin main

# 5. Deploy to cPanel
git push cpanel main

# 6. Verify deployment
# Check your website to ensure changes are live
```

### Rollback to Previous Version

If something goes wrong:

```bash
# Find the commit you want to rollback to
git log --oneline

# Create a new commit that undoes recent changes
git revert HEAD

# Or reset to a specific commit (use with caution)
git reset --hard COMMIT_HASH

# Push to cPanel to deploy the rollback
git push cpanel main --force
```

## Additional Resources

- [cPanel Git Version Control Documentation](https://docs.cpanel.net/cpanel/files/git-version-control/)
- [Git Documentation](https://git-scm.com/doc)
- PHP version requirements: PHP 8.0+

## Support

For issues related to:
- **Git/GitHub**: Check the repository issues
- **cPanel Configuration**: Contact your hosting provider
- **Application Issues**: Refer to the application documentation
