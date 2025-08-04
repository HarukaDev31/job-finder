@echo off
echo 🚀 Iniciando desarrollo separado...
echo.

echo 📡 Iniciando Laravel (puerto 8000)...
start "Laravel Server" cmd /k "php artisan serve --port=8000"

echo ⚡ Iniciando Vite (puerto 5173)...
start "Vite Server" cmd /k "npm run dev:frontend"

echo.
echo ✅ Servidores iniciados:
echo    🌐 Frontend: http://localhost:5173
echo    🔌 API: http://localhost:8000/api
echo.
echo Presiona cualquier tecla para cerrar esta ventana...
pause >nul 