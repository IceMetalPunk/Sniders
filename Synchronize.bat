@echo off
echo "Syncing from GitHub..."
cd C:\wamp\www
git fetch
git reset --hard origin/master
echo "Done!"