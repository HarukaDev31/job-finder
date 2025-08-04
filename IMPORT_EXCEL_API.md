# API de Importación de Excel - Job Finder

## Descripción General

Esta API permite importar datos de trabajos, postulantes y postulaciones desde archivos Excel. El sistema detecta automáticamente el tipo de registro y lo inserta en las tablas correspondientes.

## Características Principales

- ✅ **Detección automática** de tipo (TRABAJO, POSTULANTE, POSTULACION)
- ✅ **Creación automática** de usuarios para postulantes
- ✅ **Validación de datos** en tiempo real
- ✅ **Plantilla descargable** con ejemplos y validaciones
- ✅ **Transacciones seguras** con rollback en caso de error
- ✅ **Estadísticas detalladas** del proceso
- ✅ **Comando Artisan** para importación desde línea de comandos

## Autenticación

Todos los endpoints requieren autenticación JWT. Incluye el token en el header de autorización:

```
Authorization: Bearer {token}
```

## Endpoints Disponibles

### 1. Importar Archivo Excel

**Endpoint:** `POST /api/import/excel`

**Descripción:** Sube y procesa un archivo Excel con datos de trabajos, postulantes y postulaciones.

#### Parámetros

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `excel_file` | file | Sí | Archivo Excel (.xlsx, .xls) máximo 10MB |

#### Ejemplo de Request

```bash
curl -X POST "https://api.jobfinder.com/api/import/excel" \
  -H "Authorization: Bearer {token}" \
  -F "excel_file=@datos_jobfinder.xlsx"
```

#### Respuesta Exitosa (200)

```json
{
  "success": true,
  "message": "Importación completada exitosamente",
  "data": "=== RESULTADOS DE LA IMPORTACIÓN ===\nTrabajos:\n  - Creados: 5\n  - Actualizados: 0\n  - Errores: 0\nPostulantes:\n  - Creados: 8\n  - Actualizados: 0\n  - Errores: 0\nPostulaciones:\n  - Creados: 3\n  - Actualizados: 0\n  - Errores: 0\nUsuarios:\n  - Creados: 8\n  - Actualizados: 0\n  - Errores: 0\n\nTotal de registros procesados: 16"
}
```

#### Respuesta de Error (500)

```json
{
  "success": false,
  "message": "Error durante la importación: Detalle del error específico"
}
```

### 2. Descargar Plantilla

**Endpoint:** `GET /api/import/template`

**Descripción:** Descarga una plantilla Excel con la estructura correcta y ejemplos.

#### Ejemplo de Request

```bash
curl -X GET "https://api.jobfinder.com/api/import/template" \
  -H "Authorization: Bearer {token}" \
  --output plantilla_importacion_jobfinder.xlsx
```

#### Respuesta

Retorna el archivo Excel directamente para descarga.

### 3. Obtener Estadísticas

**Endpoint:** `GET /api/import/stats`

**Descripción:** Obtiene estadísticas generales de los datos importados.

#### Ejemplo de Request

```bash
curl -X GET "https://api.jobfinder.com/api/import/stats" \
  -H "Authorization: Bearer {token}"
```

#### Respuesta Exitosa (200)

```json
{
  "success": true,
  "data": {
    "trabajos": 150,
    "postulantes": 75,
    "postulaciones": 25,
    "usuarios": 200
  }
}
```

## Estructura del Excel

### Columnas Requeridas

| Columna | Posición | Descripción | Ejemplo | Obligatorio |
|---------|----------|-------------|---------|-------------|
| TIPO | A | Tipo de registro | TRABAJO, POSTULANTE, POSTULACION | Sí |
| TITULO | B | Título del trabajo | Desarrollador Frontend | Solo TRABAJO |
| DESCRIPCION | C | Descripción del trabajo | Desarrollar interfaces web | Solo TRABAJO |
| SUELDO | D | Salario del trabajo | 2500000 | Solo TRABAJO |
| EMAIL | E | Email del postulante | juan@email.com | Solo POSTULANTE |
| NOMBRE | F | Nombre del postulante | Juan Pérez | Solo POSTULANTE |
| DOCUMENTO | G | Número de documento | 12345678 | Solo POSTULANTE |
| TRABAJO_ID | H | ID del trabajo | 1 | Solo POSTULACION |
| POSTULANTE_ID | I | ID del postulante | 1 | Solo POSTULACION |
| MENSAJE | J | Mensaje de postulación | Me interesa el puesto | Solo POSTULACION |
| ESTADO | K | Estado de postulación | pendiente | Solo POSTULACION |

### Ejemplo de Datos

| TIPO | TITULO | DESCRIPCION | SUELDO | EMAIL | NOMBRE | DOCUMENTO | TRABAJO_ID | POSTULANTE_ID | MENSAJE | ESTADO |
|------|--------|-------------|--------|-------|--------|-----------|------------|---------------|---------|--------|
| TRABAJO | Desarrollador Frontend | Desarrollar interfaces web con Vue.js | 2500000 | | | | | | | |
| POSTULANTE | | | | juan@email.com | Juan Pérez | 12345678 | | | | |
| POSTULACION | | | | | | | 1 | 1 | Me interesa mucho el puesto | pendiente |

## Lógica de Procesamiento

### Para Trabajos (TIPO = TRABAJO)

1. **Validar datos requeridos**
   - Título no puede estar vacío
   - Descripción no puede estar vacía
   - Sueldo debe ser mayor a 0

2. **Crear/Actualizar Trabajo**
   - Si el trabajo existe (por título), se actualiza
   - Si no existe, se crea uno nuevo
   - Se asigna estado "activo"

### Para Postulantes (TIPO = POSTULANTE)

1. **Validar datos requeridos**
   - Email no puede estar vacío
   - Nombre no puede estar vacío
   - Documento no puede estar vacío

2. **Crear/Actualizar Usuario**
   - Si el usuario existe (por email), se actualiza
   - Si no existe, se crea un nuevo usuario con rol "postulante"
   - Se asigna contraseña temporal "password123"

3. **Crear/Actualizar Postulante**
   - Se busca por user_id
   - Si existe, se actualiza
   - Si no existe, se crea uno nuevo
   - Se asignan valores por defecto (tipo documento: CC, fecha nacimiento: 25 años atrás)

### Para Postulaciones (TIPO = POSTULACION)

1. **Validar datos requeridos**
   - TRABAJO_ID debe ser mayor a 0
   - POSTULANTE_ID debe ser mayor a 0
   - Mensaje no puede estar vacío

2. **Crear/Actualizar Postulación**
   - Se busca por trabajo_id y postulante_id
   - Si existe, se actualiza
   - Si no existe, se crea una nueva
   - Se asigna estado por defecto "pendiente"

## Validaciones

### Validaciones del Archivo

- ✅ Formato: .xlsx o .xls
- ✅ Tamaño máximo: 10MB
- ✅ Datos desde fila 1 (sin encabezados especiales)
- ✅ Columnas requeridas según tipo

### Validaciones de Datos

- ✅ Tipo debe ser "TRABAJO", "POSTULANTE", o "POSTULACION"
- ✅ Email válido para postulantes
- ✅ Sueldo numérico y mayor a 0 para trabajos
- ✅ IDs numéricos y mayores a 0 para postulaciones

### Validaciones de Negocio

- ✅ Usuarios únicos por email
- ✅ Postulantes únicos por user_id
- ✅ Postulaciones únicas por trabajo_id + postulante_id
- ✅ Trabajos únicos por título

## Manejo de Errores

### Tipos de Errores

1. **Errores de Archivo**
   - Archivo no encontrado
   - Formato no válido
   - Tamaño excesivo

2. **Errores de Datos**
   - Columnas faltantes
   - Datos inválidos
   - Tipos incorrectos

3. **Errores de Base de Datos**
   - Restricciones de integridad
   - Errores de conexión
   - Transacciones fallidas

### Recuperación de Errores

- ✅ **Rollback automático** en caso de error
- ✅ **Logging detallado** de errores
- ✅ **Continuación** desde el último registro válido
- ✅ **Reporte de estadísticas** de errores

## Comando Artisan

También puedes usar el comando directamente desde la línea de comandos:

```bash
php artisan import:jobs-excel /ruta/al/archivo.xlsx
```

### Parámetros del Comando

| Parámetro | Descripción |
|-----------|-------------|
| `file` | Ruta completa al archivo Excel |

### Ejemplo de Uso

```bash
# Importar archivo
php artisan import:jobs-excel /home/user/datos_jobfinder.xlsx

# Ver ayuda
php artisan import:jobs-excel --help
```

## Valores por Defecto

### Para Usuarios Nuevos

- **Rol**: postulante
- **Contraseña**: password123 (temporal)
- **Email verificado**: null

### Para Postulantes Nuevos

- **Tipo de Documento**: CC
- **Fecha de Nacimiento**: 25 años atrás
- **Apellidos**: vacío

### Para Trabajos Nuevos

- **Estado**: activo (true)
- **Fecha de Creación**: fecha actual

### Para Postulaciones Nuevas

- **Estado**: pendiente
- **CV Path**: null
- **Fecha de Creación**: fecha actual

## Estadísticas de Procesamiento

### Contadores Disponibles

- **Trabajos**: Creados, actualizados, errores
- **Postulantes**: Creados, actualizados, errores
- **Postulaciones**: Creados, actualizados, errores
- **Usuarios**: Creados, actualizados, errores
- **Total**: Registros procesados

### Ejemplo de Reporte

```
=== RESULTADOS DE LA IMPORTACIÓN ===
Trabajos:
  - Creados: 5
  - Actualizados: 0
  - Errores: 0
Postulantes:
  - Creados: 8
  - Actualizados: 0
  - Errores: 0
Postulaciones:
  - Creados: 3
  - Actualizados: 0
  - Errores: 0
Usuarios:
  - Creados: 8
  - Actualizados: 0
  - Errores: 0

Total de registros procesados: 16
```

## Códigos de Estado HTTP

| Código | Descripción |
|--------|-------------|
| 200 | OK - Importación exitosa |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - Token inválido |
| 413 | Payload Too Large - Archivo muy grande |
| 500 | Internal Server Error - Error del servidor |

## Componente Frontend

### Uso del Componente

```vue
<template>
  <ImportExcel />
</template>

<script>
import { ImportExcel } from '@/components'

export default {
  components: {
    ImportExcel
  }
}
</script>
```

### Características del Componente

- ✅ **Drag & Drop** para subir archivos
- ✅ **Validación en tiempo real** de archivos
- ✅ **Descarga de plantilla** integrada
- ✅ **Estadísticas en tiempo real**
- ✅ **Reporte visual** de resultados
- ✅ **Instrucciones detalladas** integradas

## Notas Importantes

1. **Autenticación**: Todos los endpoints requieren JWT válido
2. **Transacciones**: Todo el proceso se ejecuta en una transacción
3. **Duplicados**: El sistema evita duplicados basándose en campos únicos
4. **Plantilla**: Se recomienda usar la plantilla oficial para evitar errores
5. **Logs**: Todos los errores se registran en los logs del sistema
6. **Contraseñas**: Los usuarios nuevos reciben contraseña temporal "password123"

## Dependencias Requeridas

- **Laravel Excel**: Para lectura de archivos Excel
- **Carbon**: Para manejo de fechas
- **PhpSpreadsheet**: Para procesamiento de Excel (incluido en Laravel Excel)

## Versión de la API

Esta documentación corresponde a la versión 1.0 de la API de Importación de Excel para Job Finder.

## Soporte

Para soporte técnico o preguntas sobre la importación:
- Revisar los logs del sistema
- Verificar la estructura del Excel
- Usar la plantilla oficial
- Contactar al equipo de desarrollo 