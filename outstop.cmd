@echo off
title DCTech Shop - Auto Stop
taskkill /F /IM php.exe /T >nul 2>&1
taskkill /F /IM node.exe /T >nul 2>&1
