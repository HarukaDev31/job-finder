#!/bin/bash

echo "🚀 Iniciando desarrollo separado..."
echo ""

# Función para limpiar procesos al salir
cleanup() {
    echo ""
    echo "🛑 Deteniendo servidores..."
    kill $LARAVEL_PID $VITE_PID 2>/dev/null
    exit 0
}

# Capturar Ctrl+C
trap cleanup SIGINT

# Iniciar Laravel en background
echo "📡 Iniciando Laravel (puerto 8000)..."
php artisan serve --port=8000 &
LARAVEL_PID=$!

# Esperar un momento para que Laravel inicie
sleep 3

# Iniciar Vite en background
echo "⚡ Iniciando Vite (puerto 5173)..."
npm run dev:frontend &
VITE_PID=$!

echo ""
echo "✅ Servidores iniciados:"
echo "   🌐 Frontend: http://localhost:5173"
echo "   🔌 API: http://localhost:8000/api"
echo ""
echo "Presiona Ctrl+C para detener ambos servidores"
echo ""

# Mantener el script corriendo
wait 