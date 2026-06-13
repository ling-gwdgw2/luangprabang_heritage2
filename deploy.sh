#!/bin/bash

# deploy.sh - ສ້າງຂຶ້ນເພື່ອອັບເດດ Code ໄປທີ່ GitHub ແລະ Deploy ຂຶ້ນ Railway
# (สคริปต์อัปเดตโค้ดไปยัง GitHub และ Deploy ขึ้น Railway อัตโนมัติ)

echo "=========================================="
echo "  Starting Project Update & Deployment    "
echo "=========================================="

# 1. ກວດສອບຄວາມພ້ອມຂອງ Git (ตรวจสอบความพร้อมของ Git)
if [ -f "$HOME/.local/bin/git" ]; then
    GIT_BIN="$HOME/.local/bin/git"
    export GIT_EXEC_PATH="$HOME/.local/lib/git-core"
    export PATH="$HOME/.local/lib/git-core:$HOME/.local/bin:$PATH"
elif [ -f "/home/ling/git_local/usr/bin/git" ]; then
    GIT_BIN="/home/ling/git_local/usr/bin/git"
    export GIT_EXEC_PATH="/home/ling/git_local/usr/lib/git-core"
    export PATH="/home/ling/git_local/usr/lib/git-core:/home/ling/git_local/usr/bin:$PATH"
elif command -v git &> /dev/null; then
    GIT_BIN="git"
else
    echo "ไม่พบคำสั่ง git ในระบบ กรุณาติดตั้ง git ก่อนใช้งาน"
    exit 1
fi

# 2. ກວດສອບຄວາມພ້ອມຂອງ Railway CLI (ตรวจสอบความพร้อมของ Railway)
RAILWAY_BIN="railway"
if ! command -v railway &> /dev/null; then
    if [ -f "$HOME/.local/bin/railway" ]; then
        RAILWAY_BIN="$HOME/.local/bin/railway"
    else
        echo "ไม่พบ Railway CLI ในระบบ กรุณาติดตั้งก่อนใช้งาน"
        exit 1
    fi
fi

# 3. ອັບເດດ Code ໄປທີ່ GitHub (อัปโหลดโค้ดไปยัง GitHub)
echo -e "\n--> 1. Syncing changes with GitHub..."
$GIT_BIN status -s

# รับข้อความ Commit (Commit Message)
commit_msg="$1"

if [ -z "$commit_msg" ]; then
    commit_msg="Update system configuration and features"
fi

$GIT_BIN add .
$GIT_BIN commit -m "$commit_msg"
$GIT_BIN push origin main

if [ $? -eq 0 ]; then
    echo "อัปเดตขึ้น GitHub สำเร็จ!"
else
    echo "เกิดข้อผิดพลาดในการ Push ไปยัง GitHub"
    exit 1
fi

# 4. Deploy ຂຶ້ນ Railway (รันคำสั่ง Deploy ขึ้นเซิร์ฟเวอร์ Railway)
echo -e "\n--> 2. Deploying to Railway..."
$RAILWAY_BIN up

if [ $? -eq 0 ]; then
    echo "=========================================="
    echo "อัปเดตและ Deploy ไปยังเซิร์ฟเวอร์สำเร็จ!"
    echo "=========================================="
else
    echo "เกิดข้อผิดพลาดในการ Deploy ขึ้น Railway"
    exit 1
fi
