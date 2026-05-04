#!/bin/bash
# ============================================
# Domain-TIK Git Auto-Push Script (Bash)
# ============================================
# Untuk Linux/Mac atau Windows (WSL/Git Bash)

cd "c:\laragon\www\Domain-TIK" || exit 1

echo ""
echo "[INFO] Checking git status..."
git status

# Check if ada staged changes
if git diff --cached --quiet; then
    echo "[INFO] No staged changes to push"
    exit 0
fi

echo ""
echo "[INFO] Pushing to origin main..."
git push origin main

if [ $? -eq 0 ]; then
    echo "[SUCCESS] Push to GitHub successful!"
else
    echo "[ERROR] Push to GitHub failed. Check connection or credentials."
    exit 1
fi

echo ""
echo "[INFO] Done"
