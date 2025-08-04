# Estructura de Componentes Vue.js

## 📁 Organización de Carpetas

```
components/
├── common/           # Componentes reutilizables
│   ├── Pagination.vue
│   └── index.js
├── dashboard/        # Componentes del dashboard
│   ├── Dashboard.vue
│   ├── AdminDashboard.vue
│   └── index.js
├── jobs/            # Componentes de gestión de trabajos
│   ├── Jobs.vue
│   ├── AdminJobs.vue
│   └── index.js
├── applications/    # Componentes de postulaciones
│   ├── Applications.vue
│   ├── AdminApplications.vue
│   └── index.js
├── Auth/           # Componentes de autenticación
│   ├── Login.vue
│   ├── Register.vue
│   └── index.js
├── charts/         # Componentes de gráficos
│   ├── BarChart.vue
│   ├── DoughnutChart.vue
│   ├── LineChart.vue
│   └── index.js
├── layout/         # Componentes de layout
│   └── App.vue
├── Home.vue        # Componente principal
├── index.js        # Exportaciones principales
└── README.md       # Esta documentación
```

## 🎯 Categorías de Componentes

### 🔧 Common (Comunes)
Componentes reutilizables en toda la aplicación:
- **Pagination.vue**: Componente de paginación

### 📊 Dashboard
Componentes para mostrar estadísticas y métricas:
- **Dashboard.vue**: Dashboard para usuarios normales
- **AdminDashboard.vue**: Dashboard para administradores

### 💼 Jobs (Trabajos)
Componentes para gestión de ofertas de trabajo:
- **Jobs.vue**: Lista y gestión de trabajos para usuarios
- **AdminJobs.vue**: Gestión completa de trabajos para administradores

### 📝 Applications (Postulaciones)
Componentes para gestión de postulaciones:
- **Applications.vue**: Mis postulaciones para usuarios
- **AdminApplications.vue**: Gestión de todas las postulaciones

### 🔐 Auth (Autenticación)
Componentes de autenticación:
- **Login.vue**: Formulario de inicio de sesión
- **Register.vue**: Formulario de registro

### 📈 Charts (Gráficos)
Componentes de visualización de datos:
- **BarChart.vue**: Gráfico de barras
- **DoughnutChart.vue**: Gráfico de dona
- **LineChart.vue**: Gráfico de líneas

### 🏗️ Layout
Componentes de estructura:
- **App.vue**: Componente raíz de la aplicación

## 📦 Uso de Importaciones

### Importación Individual
```javascript
import { Pagination } from '@/components/common';
import { Dashboard, AdminDashboard } from '@/components/dashboard';
import { Jobs, AdminJobs } from '@/components/jobs';
```

### Importación Completa
```javascript
import * as Components from '@/components';
```

### Importación Directa
```javascript
import Pagination from '@/components/common/Pagination.vue';
import Dashboard from '@/components/dashboard/Dashboard.vue';
```

## 🎨 Convenciones de Nomenclatura

### Archivos de Componentes
- **PascalCase**: `AdminDashboard.vue`
- **Descriptivo**: Nombres que describan la funcionalidad
- **Consistente**: Mismo patrón en toda la aplicación

### Carpetas
- **camelCase**: `adminDashboard/`
- **Descriptivo**: Nombres que agrupen funcionalidades relacionadas
- **Singular**: Para carpetas de componentes específicos

## 🔄 Mantenimiento

### Agregar Nuevos Componentes
1. Crear el archivo `.vue` en la carpeta correspondiente
2. Actualizar el `index.js` de la carpeta
3. Actualizar este README si es necesario

### Refactorización
- Mantener la separación de responsabilidades
- Evitar componentes demasiado grandes
- Reutilizar componentes comunes cuando sea posible

## 📋 Checklist de Componentes

- [x] Pagination (Common)
- [x] Dashboard (Dashboard)
- [x] AdminDashboard (Dashboard)
- [x] Jobs (Jobs)
- [x] AdminJobs (Jobs)
- [x] Applications (Applications)
- [x] AdminApplications (Applications)
- [x] Login (Auth)
- [x] Register (Auth)
- [x] BarChart (Charts)
- [x] DoughnutChart (Charts)
- [x] LineChart (Charts)
- [x] App (Layout)
- [x] Home (Principal)

---

**Estructura organizada para mejor mantenibilidad y escalabilidad** 🚀 