<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Buscar Trabajos - Job Finder</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <b-navbar toggleable="lg" type="dark" variant="success" class="mb-4">
            <b-navbar-brand href="#">
                <i class="fas fa-user-tie mr-2"></i>
                Job Finder - Postulante
            </b-navbar-brand>

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    <b-nav-item href="/postulante/dashboard">Dashboard</b-nav-item>
                    <b-nav-item href="#" active>Buscar Trabajos</b-nav-item>
                    <b-nav-item href="/postulante/dashboard">Mis Postulaciones</b-nav-item>
                </b-navbar-nav>

                <b-navbar-nav class="ml-auto">
                    <b-nav-item-dropdown right>
                        <template #button-content>
                            <i class="fas fa-user-circle mr-1"></i>
                            {{ auth()->user()->name }}
                        </template>
                        <b-dropdown-item href="#" @click="logout">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Cerrar Sesión
                        </b-dropdown-item>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>

        <!-- Main Content -->
        <div class="container-fluid">
            <!-- Header -->
            <b-row class="mb-4">
                <b-col>
                    <b-card bg-variant="light" border-variant="success">
                        <b-card-body class="text-center">
                            <i class="fas fa-search fa-3x text-success mb-3"></i>
                            <h3>Buscar Trabajos</h3>
                            <p class="lead">Encuentra las mejores oportunidades laborales</p>
                        </b-card-body>
                    </b-card>
                </b-col>
            </b-row>

            <!-- Filters and Search -->
            <b-card class="mb-4">
                <b-card-header>
                    <h5 class="mb-0">
                        <i class="fas fa-filter mr-2"></i>
                        Filtros de Búsqueda
                    </h5>
                </b-card-header>
                <b-card-body>
                    <b-row>
                        <b-col md="4">
                            <b-form-group label="Buscar:" label-for="search">
                                <b-input-group>
                                    <b-form-input
                                        id="search"
                                        v-model="search"
                                        placeholder="Buscar por título o descripción..."
                                        @input="filterJobs">
                                    </b-form-input>
                                    <b-input-group-append>
                                        <b-button variant="outline-secondary">
                                            <i class="fas fa-search"></i>
                                        </b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </b-form-group>
                        </b-col>
                        <b-col md="3">
                            <b-form-group label="Sueldo mínimo:" label-for="minSalary">
                                <b-form-input
                                    id="minSalary"
                                    v-model="filters.minSalary"
                                    type="number"
                                    placeholder="Sueldo mínimo"
                                    @input="filterJobs">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col md="3">
                            <b-form-group label="Sueldo máximo:" label-for="maxSalary">
                                <b-form-input
                                    id="maxSalary"
                                    v-model="filters.maxSalary"
                                    type="number"
                                    placeholder="Sueldo máximo"
                                    @input="filterJobs">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col md="2">
                            <b-form-group label="Ordenar por:" label-for="sortBy">
                                <b-form-select
                                    id="sortBy"
                                    v-model="filters.sortBy"
                                    @change="filterJobs">
                                    <option value="created_at">Más Recientes</option>
                                    <option value="sueldo">Mayor Sueldo</option>
                                    <option value="titulo">Título A-Z</option>
                                </b-form-select>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <b-button variant="outline-secondary" @click="clearFilters">
                                <i class="fas fa-times mr-1"></i>
                                Limpiar Filtros
                            </b-button>
                            <b-button variant="success" @click="refreshJobs">
                                <i class="fas fa-sync-alt mr-1"></i>
                                Actualizar
                            </b-button>
                        </b-col>
                    </b-row>
                </b-card-body>
            </b-card>

            <!-- Jobs Grid -->
            <b-row>
                <b-col>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-briefcase mr-2"></i>
                            Trabajos Disponibles
                            <b-badge variant="info" class="ml-2">@{{ filteredJobs.length }}</b-badge>
                        </h5>
                        <div>
                            <b-button-group size="sm">
                                <b-button 
                                    :variant="viewMode === 'grid' ? 'primary' : 'outline-primary'"
                                    @click="viewMode = 'grid'">
                                    <i class="fas fa-th"></i>
                                </b-button>
                                <b-button 
                                    :variant="viewMode === 'list' ? 'primary' : 'outline-primary'"
                                    @click="viewMode = 'list'">
                                    <i class="fas fa-list"></i>
                                </b-button>
                            </b-button-group>
                        </div>
                    </div>
                </b-col>
            </b-row>

            <!-- Loading State -->
            <div v-if="loading" class="text-center my-5">
                <b-spinner style="width: 3rem; height: 3rem;"></b-spinner>
                <p class="mt-3">Cargando trabajos...</p>
            </div>

            <!-- Grid View -->
            <div v-else-if="viewMode === 'grid'">
                <b-row>
                    <b-col 
                        v-for="job in paginatedJobs" 
                        :key="job.id" 
                        md="6" 
                        lg="4" 
                        class="mb-4">
                        <b-card 
                            :header="job.titulo"
                            header-bg-variant="primary"
                            header-text-variant="white"
                            class="h-100 job-card">
                            
                            <b-card-body class="d-flex flex-column">
                                <p class="card-text flex-grow-1">
                                    @{{ job.descripcion.length > 150 ? job.descripcion.substring(0, 150) + '...' : job.descripcion }}
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-success font-weight-bold h5 mb-0">
                                            $@{{ formatSalary(job.sueldo) }}
                                        </span>
                                        <b-badge variant="info">
                                            @{{ job.postulantes_count }} postulantes
                                        </b-badge>
                                    </div>
                                    
                                    <div class="text-muted small mb-3">
                                        <i class="fas fa-calendar mr-1"></i>
                                        Publicado: @{{ formatDate(job.created_at) }}
                                    </div>
                                    
                                    <b-button 
                                        variant="success" 
                                        block 
                                        @click="applyToJob(job)"
                                        :disabled="hasApplied(job.id)">
                                        <i class="fas fa-paper-plane mr-1"></i>
                                        @{{ hasApplied(job.id) ? 'Ya Postulado' : 'Postular' }}
                                    </b-button>
                                </div>
                            </b-card-body>
                        </b-card>
                    </b-col>
                </b-row>
            </div>

            <!-- List View -->
            <div v-else>
                <b-card>
                    <b-table
                        :items="filteredJobs"
                        :fields="tableFields"
                        striped
                        hover
                        responsive
                        show-empty
                        empty-text="No hay trabajos disponibles">
                        
                        <template #cell(titulo)="data">
                            <strong>@{{ data.item.titulo }}</strong>
                        </template>

                        <template #cell(descripcion)="data">
                            <span>@{{ data.item.descripcion.length > 100 ? data.item.descripcion.substring(0, 100) + '...' : data.item.descripcion }}</span>
                        </template>

                        <template #cell(sueldo)="data">
                            <span class="text-success font-weight-bold">
                                $@{{ formatSalary(data.item.sueldo) }}
                            </span>
                        </template>

                        <template #cell(postulantes_count)="data">
                            <b-badge variant="info">@{{ data.item.postulantes_count }}</b-badge>
                        </template>

                        <template #cell(created_at)="data">
                            @{{ formatDate(data.item.created_at) }}
                        </template>

                        <template #cell(actions)="data">
                            <b-button 
                                variant="success" 
                                size="sm" 
                                @click="applyToJob(data.item)"
                                :disabled="hasApplied(data.item.id)">
                                <i class="fas fa-paper-plane mr-1"></i>
                                @{{ hasApplied(data.item.id) ? 'Ya Postulado' : 'Postular' }}
                            </b-button>
                        </template>
                    </b-table>
                </b-card>
            </div>

            <!-- Pagination -->
            <b-row class="mt-4">
                <b-col>
                    <b-pagination
                        v-model="currentPage"
                        :total-rows="totalRows"
                        :per-page="perPage"
                        align="center"
                        @change="onPageChange">
                    </b-pagination>
                </b-col>
            </b-row>
        </div>

        <!-- Apply to Job Modal -->
        <b-modal
            v-model="showApplyModal"
            title="Postular a Trabajo"
            size="lg"
            @ok="submitApplication"
            @hidden="resetApplicationForm">
            
            <b-form @submit.stop.prevent="submitApplication">
                <b-form-group label="Trabajo:" label-for="job-title">
                    <b-form-input
                        id="job-title"
                        :value="selectedJob ? selectedJob.titulo : ''"
                        readonly>
                    </b-form-input>
                </b-form-group>

                <b-form-group label="Descripción del trabajo:" label-for="job-description">
                    <b-form-textarea
                        id="job-description"
                        :value="selectedJob ? selectedJob.descripcion : ''"
                        rows="3"
                        readonly>
                    </b-form-textarea>
                </b-form-group>

                <b-form-group label="Sueldo:" label-for="job-salary">
                    <b-form-input
                        id="job-salary"
                        :value="selectedJob ? '$' + formatSalary(selectedJob.sueldo) : ''"
                        readonly>
                    </b-form-input>
                </b-form-group>

                <hr>

                <b-form-group label="Mensaje de Postulación:" label-for="mensaje">
                    <b-form-textarea
                        id="mensaje"
                        v-model="applicationForm.mensaje"
                        :state="validateState('mensaje')"
                        rows="4"
                        required
                        placeholder="Escribe un mensaje explicando por qué eres el candidato ideal para este trabajo...">
                    </b-form-textarea>
                    <b-form-invalid-feedback :state="validateState('mensaje')">
                        El mensaje es obligatorio.
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group label="CV (PDF):" label-for="cv">
                    <b-form-file
                        id="cv"
                        v-model="applicationForm.cv"
                        :state="validateState('cv')"
                        accept=".pdf"
                        required
                        placeholder="Selecciona tu CV en formato PDF">
                    </b-form-file>
                    <b-form-invalid-feedback :state="validateState('cv')">
                        El CV es obligatorio y debe ser un archivo PDF.
                    </b-form-invalid-feedback>
                    <small class="form-text text-muted">
                        Máximo 5MB. Solo archivos PDF.
                    </small>
                </b-form-group>
            </b-form>

            <template #modal-footer="{ ok, cancel }">
                <b-button variant="secondary" @click="cancel">
                    Cancelar
                </b-button>
                <b-button variant="success" @click="ok" :disabled="loading">
                    <b-spinner small v-if="loading"></b-spinner>
                    <span v-text="loading ? 'Postulando...' : 'Postular'"></span>
                </b-button>
            </template>
        </b-modal>
    </div>

    <script>
        // Esperar a que Vue esté disponible
        function initVue() {
            if (typeof Vue !== 'undefined') {
                new Vue({
                    el: '#app',
                    data: {
                        loading: false,
                        jobs: [],
                        filteredJobs: [],
                        myApplications: [],
                        showApplyModal: false,
                        selectedJob: null,
                        viewMode: 'grid',
                        currentPage: 1,
                        perPage: 12,
                        search: '',
                        filters: {
                            minSalary: '',
                            maxSalary: '',
                            sortBy: 'created_at'
                        },
                        applicationForm: {
                            trabajo_id: '',
                            mensaje: '',
                            cv: null
                        },
                        errors: {},
                        tableFields: [
                            { key: 'titulo', label: 'Título', sortable: true },
                            { key: 'descripcion', label: 'Descripción', sortable: false },
                            { key: 'sueldo', label: 'Sueldo', sortable: true },
                            { key: 'postulantes_count', label: 'Postulantes', sortable: true },
                            { key: 'created_at', label: 'Fecha', sortable: true },
                            { key: 'actions', label: 'Acciones', sortable: false }
                        ]
                    },
                    computed: {
                        totalRows() {
                            return this.filteredJobs.length;
                        },
                        paginatedJobs() {
                            const start = (this.currentPage - 1) * this.perPage;
                            const end = start + this.perPage;
                            return this.filteredJobs.slice(start, end);
                        }
                    },
                    mounted() {
                        this.loadJobs();
                        this.loadMyApplications();
                    },
                    methods: {
                        loadJobs() {
                            this.loading = true;
                            axios.get('/postulante/trabajos')
                                .then(response => {
                                    this.jobs = response.data.jobs;
                                    this.filterJobs();
                                })
                                .catch(error => {
                                    this.$bvToast.toast('Error al cargar los trabajos', {
                                        title: 'Error',
                                        variant: 'danger',
                                        solid: true
                                    });
                                })
                                .finally(() => {
                                    this.loading = false;
                                });
                        },
                        loadMyApplications() {
                            axios.get('/postulante/dashboard')
                                .then(response => {
                                    this.myApplications = response.data.applications;
                                })
                                .catch(error => {
                                    console.error('Error loading applications:', error);
                                });
                        },
                        filterJobs() {
                            let filtered = this.jobs;
                            
                            // Search filter
                            if (this.search) {
                                filtered = filtered.filter(job => 
                                    job.titulo.toLowerCase().includes(this.search.toLowerCase()) ||
                                    job.descripcion.toLowerCase().includes(this.search.toLowerCase())
                                );
                            }
                            
                            // Salary filters
                            if (this.filters.minSalary) {
                                filtered = filtered.filter(job => job.sueldo >= parseFloat(this.filters.minSalary));
                            }
                            
                            if (this.filters.maxSalary) {
                                filtered = filtered.filter(job => job.sueldo <= parseFloat(this.filters.maxSalary));
                            }
                            
                            // Sort
                            filtered.sort((a, b) => {
                                switch (this.filters.sortBy) {
                                    case 'sueldo':
                                        return b.sueldo - a.sueldo;
                                    case 'titulo':
                                        return a.titulo.localeCompare(b.titulo);
                                    case 'created_at':
                                    default:
                                        return new Date(b.created_at) - new Date(a.created_at);
                                }
                            });
                            
                            this.filteredJobs = filtered;
                            this.currentPage = 1; // Reset to first page
                        },
                        clearFilters() {
                            this.search = '';
                            this.filters = {
                                minSalary: '',
                                maxSalary: '',
                                sortBy: 'created_at'
                            };
                            this.filterJobs();
                        },
                        refreshJobs() {
                            this.loadJobs();
                        },
                        hasApplied(jobId) {
                            return this.myApplications.some(app => app.trabajo_id === jobId);
                        },
                        applyToJob(job) {
                            if (this.hasApplied(job.id)) {
                                this.$bvToast.toast('Ya te has postulado a este trabajo', {
                                    title: 'Info',
                                    variant: 'info',
                                    solid: true
                                });
                                return;
                            }
                            
                            this.selectedJob = job;
                            this.applicationForm.trabajo_id = job.id;
                            this.showApplyModal = true;
                        },
                        submitApplication() {
                            this.loading = true;
                            this.errors = {};

                            const formData = new FormData();
                            formData.append('trabajo_id', this.applicationForm.trabajo_id);
                            formData.append('mensaje', this.applicationForm.mensaje);
                            formData.append('cv', this.applicationForm.cv);

                            axios.post('/postulante/postular', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then(response => {
                                this.$bvToast.toast('Postulación enviada exitosamente', {
                                    title: 'Éxito',
                                    variant: 'success',
                                    solid: true
                                });
                                this.showApplyModal = false;
                                this.loadMyApplications(); // Refresh applications
                            })
                            .catch(error => {
                                if (error.response && error.response.data.errors) {
                                    this.errors = error.response.data.errors;
                                } else {
                                    this.$bvToast.toast('Error al enviar postulación', {
                                        title: 'Error',
                                        variant: 'danger',
                                        solid: true
                                    });
                                }
                            })
                            .finally(() => {
                                this.loading = false;
                            });
                        },
                        resetApplicationForm() {
                            this.applicationForm = {
                                trabajo_id: '',
                                mensaje: '',
                                cv: null
                            };
                            this.selectedJob = null;
                            this.errors = {};
                        },
                        validateState(field) {
                            if (this.errors[field]) {
                                return false;
                            }
                            return null;
                        },
                        formatSalary(salary) {
                            return parseFloat(salary).toLocaleString('es-CO');
                        },
                        formatDate(date) {
                            return new Date(date).toLocaleDateString('es-CO');
                        },
                        onPageChange(page) {
                            this.currentPage = page;
                        },
                        logout() {
                            axios.post('/logout')
                                .then(() => {
                                    window.location.href = '/';
                                });
                        }
                    }
                });
            } else {
                setTimeout(initVue, 100);
            }
        }

        // Inicializar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initVue);
        } else {
            initVue();
        }
    </script>

    <style>
        .job-card {
            transition: transform 0.2s ease-in-out;
        }
        
        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</body>
</html> 