<template>
  <div class="import-excel">
    <b-card>
      <b-card-header>
        <h5 class="mb-0">
          <i class="fas fa-file-excel mr-2"></i>
          Importar Datos desde Excel
        </h5>
      </b-card-header>
      <b-card-body>
        <b-row>
          <b-col md="6">
            <div class="upload-section">
              <h6>Subir Archivo Excel</h6>
              <p class="text-muted">
                Sube un archivo Excel (.xlsx, .xls) con los datos a importar.
                Máximo 10MB.
              </p>
              
              <b-form-file
                v-model="selectedFile"
                accept=".xlsx,.xls"
                placeholder="Selecciona un archivo Excel..."
                drop-placeholder="Arrastra el archivo aquí..."
                @change="onFileSelected"
              ></b-form-file>
              
              <div class="mt-3">
                <b-button 
                  variant="primary" 
                  @click="uploadFile"
                  :disabled="!selectedFile || uploading"
                >
                  <b-spinner small v-if="uploading"></b-spinner>
                  <i class="fas fa-upload mr-1" v-else></i>
                  {{ uploading ? 'Subiendo...' : 'Importar Datos' }}
                </b-button>
                
                <b-button 
                  variant="outline-secondary" 
                  @click="downloadTemplate"
                  :disabled="downloading"
                  class="ml-2"
                >
                  <b-spinner small v-if="downloading"></b-spinner>
                  <i class="fas fa-download mr-1" v-else></i>
                  {{ downloading ? 'Descargando...' : 'Descargar Plantilla' }}
                </b-button>
              </div>
            </div>
          </b-col>
          
          <b-col md="6">
            <div class="stats-section">
              <h6>Estadísticas del Sistema</h6>
              <div v-if="loadingStats" class="text-center">
                <b-spinner></b-spinner>
                <p>Cargando estadísticas...</p>
              </div>
              <div v-else-if="stats" class="stats-grid">
                <div class="stat-item">
                  <div class="stat-number">{{ stats.trabajos }}</div>
                  <div class="stat-label">Trabajos</div>
                </div>
                <div class="stat-item">
                  <div class="stat-number">{{ stats.postulantes }}</div>
                  <div class="stat-label">Postulantes</div>
                </div>
                <div class="stat-item">
                  <div class="stat-number">{{ stats.postulaciones }}</div>
                  <div class="stat-label">Postulaciones</div>
                </div>
                <div class="stat-item">
                  <div class="stat-number">{{ stats.usuarios }}</div>
                  <div class="stat-label">Usuarios</div>
                </div>
              </div>
            </div>
          </b-col>
        </b-row>

        <!-- Resultados de Importación -->
        <div v-if="importResult" class="mt-4">
          <b-alert :variant="importResult.success ? 'success' : 'danger'" show>
            <h6>{{ importResult.success ? '✅ Importación Exitosa' : '❌ Error en Importación' }}</h6>
            <p>{{ importResult.message }}</p>
            <pre v-if="importResult.output" class="import-output">{{ importResult.output }}</pre>
          </b-alert>
        </div>

        <!-- Instrucciones -->
        <div class="mt-4">
          <h6>Instrucciones de Uso</h6>
          <div class="instructions">
            <h6>Estructura del Excel:</h6>
            <p>El archivo debe contener las siguientes columnas:</p>
            <ul>
              <li><strong>TIPO:</strong> TRABAJO, POSTULANTE, o POSTULACION</li>
              <li><strong>TITULO:</strong> Título del trabajo (solo para TRABAJO)</li>
              <li><strong>DESCRIPCION:</strong> Descripción del trabajo (solo para TRABAJO)</li>
              <li><strong>SUELDO:</strong> Salario del trabajo (solo para TRABAJO)</li>
              <li><strong>EMAIL:</strong> Email del postulante (solo para POSTULANTE)</li>
              <li><strong>NOMBRE:</strong> Nombre del postulante (solo para POSTULANTE)</li>
              <li><strong>DOCUMENTO:</strong> Número de documento (solo para POSTULANTE)</li>
              <li><strong>TRABAJO_ID:</strong> ID del trabajo (solo para POSTULACION)</li>
              <li><strong>POSTULANTE_ID:</strong> ID del postulante (solo para POSTULACION)</li>
              <li><strong>MENSAJE:</strong> Mensaje de postulación (solo para POSTULACION)</li>
              <li><strong>ESTADO:</strong> Estado de la postulación (solo para POSTULACION)</li>
            </ul>
            
            <h6>Ejemplos:</h6>
            <div class="example-table">
              <table class="table table-sm table-bordered">
                <thead>
                  <tr>
                    <th>TIPO</th>
                    <th>TITULO</th>
                    <th>DESCRIPCION</th>
                    <th>SUELDO</th>
                    <th>EMAIL</th>
                    <th>NOMBRE</th>
                    <th>DOCUMENTO</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>TRABAJO</td>
                    <td>Desarrollador Frontend</td>
                    <td>Desarrollar interfaces web con Vue.js</td>
                    <td>2500000</td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>POSTULANTE</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>juan@email.com</td>
                    <td>Juan Pérez</td>
                    <td>12345678</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ImportExcel',
  data() {
    return {
      selectedFile: null,
      uploading: false,
      downloading: false,
      loadingStats: false,
      stats: null,
      importResult: null
    }
  },
  mounted() {
    this.loadStats()
  },
  methods: {
    onFileSelected(event) {
      const file = event.target.files[0]
      if (file) {
        if (file.size > 10 * 1024 * 1024) { // 10MB
          this.$bvToast.toast('El archivo es demasiado grande. Máximo 10MB.', {
            title: 'Error',
            variant: 'danger',
            solid: true
          })
          this.selectedFile = null
          return
        }
        
        if (!file.name.match(/\.(xlsx|xls)$/i)) {
          this.$bvToast.toast('Solo se permiten archivos Excel (.xlsx, .xls)', {
            title: 'Error',
            variant: 'danger',
            solid: true
          })
          this.selectedFile = null
          return
        }
      }
    },
    
    async uploadFile() {
      if (!this.selectedFile) {
        this.$bvToast.toast('Selecciona un archivo primero', {
          title: 'Error',
          variant: 'warning',
          solid: true
        })
        return
      }

      this.uploading = true
      this.importResult = null

      try {
        const formData = new FormData()
        formData.append('excel_file', this.selectedFile)

        const response = await axios.post('/api/import/excel', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          this.importResult = {
            success: true,
            message: response.data.message,
            output: response.data.data
          }
          this.$bvToast.toast('Importación completada exitosamente', {
            title: 'Éxito',
            variant: 'success',
            solid: true
          })
          this.loadStats() // Recargar estadísticas
        } else {
          this.importResult = {
            success: false,
            message: response.data.message
          }
        }

      } catch (error) {
        console.error('Error uploading file:', error)
        this.importResult = {
          success: false,
          message: error.response?.data?.message || 'Error al subir el archivo'
        }
        this.$bvToast.toast('Error al subir el archivo', {
          title: 'Error',
          variant: 'danger',
          solid: true
        })
      } finally {
        this.uploading = false
        this.selectedFile = null
      }
    },

    async downloadTemplate() {
      this.downloading = true

      try {
        const response = await axios.get('/api/import/template', {
          responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'plantilla_importacion_jobfinder.xlsx')
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)

        this.$bvToast.toast('Plantilla descargada exitosamente', {
          title: 'Éxito',
          variant: 'success',
          solid: true
        })

      } catch (error) {
        console.error('Error downloading template:', error)
        this.$bvToast.toast('Error al descargar la plantilla', {
          title: 'Error',
          variant: 'danger',
          solid: true
        })
      } finally {
        this.downloading = false
      }
    },

    async loadStats() {
      this.loadingStats = true

      try {
        const response = await axios.get('/api/import/stats')
        if (response.data.success) {
          this.stats = response.data.data
        }
      } catch (error) {
        console.error('Error loading stats:', error)
      } finally {
        this.loadingStats = false
      }
    }
  }
}
</script>

<style scoped>
.import-excel {
  padding: 20px;
}

.upload-section, .stats-section {
  padding: 20px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  height: 100%;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px;
}

.stat-item {
  text-align: center;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  color: #007bff;
}

.stat-label {
  font-size: 0.9rem;
  color: #6c757d;
  margin-top: 5px;
}

.import-output {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 4px;
  padding: 15px;
  font-family: 'Courier New', monospace;
  font-size: 0.9rem;
  white-space: pre-wrap;
  max-height: 300px;
  overflow-y: auto;
}

.instructions {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
}

.example-table {
  margin-top: 15px;
}

.example-table table {
  font-size: 0.85rem;
}

.example-table th {
  background: #e9ecef;
  font-weight: bold;
}
</style> 