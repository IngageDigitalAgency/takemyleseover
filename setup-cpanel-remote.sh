#!/bin/bash
# setup-cpanel-remote.sh
# Helper script to configure cPanel remote repository

echo "==================================="
echo "cPanel Remote Setup Helper"
echo "==================================="
echo ""

# Check if git remote 'cpanel' already exists
if git remote get-url cpanel &>/dev/null; then
    echo "⚠️  A 'cpanel' remote already exists:"
    git remote get-url cpanel
    echo ""
    read -p "Do you want to update it? (y/n) " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Setup cancelled."
        exit 0
    fi
    UPDATE_MODE=true
else
    UPDATE_MODE=false
fi

# Get cPanel details
echo "Please provide your cPanel connection details:"
echo ""

read -p "cPanel username: " CPANEL_USER
read -p "Domain or server IP: " CPANEL_DOMAIN
read -p "Repository name (default: takemyleseover): " REPO_NAME

# Use default if repository name is empty
REPO_NAME=${REPO_NAME:-takemyleseover}

# Construct the remote URL
REMOTE_URL="ssh://${CPANEL_USER}@${CPANEL_DOMAIN}/home/${CPANEL_USER}/repositories/${REPO_NAME}"

echo ""
echo "Remote URL will be: $REMOTE_URL"
echo ""

# Add or update the remote
if [ "$UPDATE_MODE" = true ]; then
    git remote set-url cpanel "$REMOTE_URL"
    echo "✓ Updated 'cpanel' remote"
else
    git remote add cpanel "$REMOTE_URL"
    echo "✓ Added 'cpanel' remote"
fi

echo ""
echo "==================================="
echo "Setup Complete!"
echo "==================================="
echo ""
echo "Next steps:"
echo "1. Update .cpanel.yml with your cPanel username"
echo "   Replace: /home/username/public_html/"
echo "   With:    /home/${CPANEL_USER}/public_html/"
echo ""
echo "2. Test SSH connection:"
echo "   ssh ${CPANEL_USER}@${CPANEL_DOMAIN}"
echo ""
echo "3. Deploy to cPanel:"
echo "   git push cpanel main"
echo ""
echo "For detailed instructions, see DEPLOY.md"
