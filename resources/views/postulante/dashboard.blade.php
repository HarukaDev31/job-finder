<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard Postulante - Job Finder</title>

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
                    <b-nav-item href="#" active>Dashboard</b-nav-item>
                    <b-nav-item href="#" @click="showJobs">Buscar Trabajos</b-nav-item>
                    <b-nav-item href="#" @click="showMyApplications">Mis Postulaciones</b-nav-item>
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
            <!-- Welcome Section -->
            <b-row class="mb-4">
                <b-col>
                    <b-card bg-variant="light" border-variant="success">
                        <b-card-body class="text-center">
                            <i class="fas fa-user-tie fa-3x text-success mb-3"></i>
                            <h3>¡Bienvenido, {{ auth()->user()->name }}!</h3>
                            <p class="lead">Encuentra tu trabajo ideal y postúlate a las mejores ofertas</p>
                        </b-card-body>
                    </b-card>
                </b-col>
            </b-row>

            <!-- Stats Cards -->
            <b-row class="mb-4">
                <b-col md="3">
                    <b-card bg-variant="primary" text-variant="white" class="text-center">
                        <b-card-body>
                            <i class="fas fa-briefcase fa-2x mb-2"></i>
                            <h4>@{{ availableJobs }}</h4>
                            <p class="mb-0">Trabajos Disponibles</p>
                        </b-card-body>
                    </b-card>
                </b-col>
                <b-col md="3">
                    <b-card bg-variant="success" text-variant="white" class="text-center">
                        <b-card-body>
                            <i class="fas fa-paper-plane fa-2x mb-2"></i>
                            <h4>@{{ myApplications }}</h4>
                            <p class="mb-0">Mis Postulaciones</p>
                        </b-card-body>
                    </b-card>
                </b-col>
                <b-col md="3">
                    <b-card bg-variant="info" text-variant="white" class="text-center">
                        <b-card-body>
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h4>@{{ pendingApplications }}</h4>
                            <p class="mb-0">Pendientes</p>
                        </b-card-body>
                    </b-card>
                </b-col>
                <b-col md="3">
                    <b-card bg-variant="warning" text-variant="white" class="text-center">
                        <b-card-body>
                            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                            <h4>@{{ recentApplications }}</h4>
                            <p class="mb-0">Esta Semana</p>
                        </b-card-body>
                    </b-card>
                </b-col>
            </b-row>

            <!-- Content Tabs -->
            <b-card>
                <b-tabs content-class="mt-3" fill>
                    <!-- Recent Jobs Tab -->
                    <b-tab title="Trabajos Recientes" active>
                        <b-card-body>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock mr-2"></i>
                                    Trabajos Recientes
                                </h5>
                                <b-button variant="success" @click="showJobs">
                                    <i class="fas fa-search mr-1"></i>
                                    Ver Todos
                                </b-button>
                            </div>

                            <b-table
                                :items="recentJobs"
                                :fields="jobFields"
                                striped
                                hover
                                responsive
                                :busy="loading"
                                show-empty
                                empty-text="No hay trabajos recientes">
                                
                                <template #cell(titulo)="data">
                                    <strong>@{{ data.item.titulo }}</strong>
                                </template>

                                <template #cell(sueldo)="data">
                                    <span class="text-success font-weight-bold">
                                        $@{{ formatSalary(data.item.sueldo) }}
                                    </span>
                                </template>

                                <template #cell(actions)="data">
                                    <b-button variant="primary" size="sm" @click="applyToJob(data.item)">
                                        <i class="fas fa-paper-plane mr-1"></i>
                                        Postular
                                    </b-button>
                                </template>

                                <template #table-busy>
                                    <div class="text-center my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>Cargando...</strong>
                                    </div>
                                </template>
                            </b-table>
                        </b-card-body>
                    </b-tab>

                    <!-- My Applications Tab -->
                    <b-tab title="Mis Postulaciones">
                        <b-card-body>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-list mr-2"></i>
                                    Mis Postulaciones
                                </h5>
                                <b-button variant="outline-secondary" @click="refreshApplications">
                                    <i class="fas fa-sync-alt mr-1"></i>
                                    Actualizar
                                </b-button>
                            </div>

                            <b-table
                                :items="myApplications"
                                :fields="applicationFields"
                                striped
                                hover
                                responsive
                                :busy="loading"
                                show-empty
                                empty-text="No tienes postulaciones">
                                
                                <template #cell(trabajo.titulo)="data">
                                    <strong>@{{ data.item.trabajo.titulo }}</strong>
                                </template>

                                <template #cell(trabajo.sueldo)="data">
                                    <span class="text-success font-weight-bold">
                                        $@{{ formatSalary(data.item.trabajo.sueldo) }}
                                    </span>
                                </template>

                                <template #cell(estado)="data">
                                    <b-badge :variant="getStatusVariant(data.item.estado)">
                                        @{{ getStatusText(data.item.estado) }}
                                    </b-badge>
                                </template>

                                <template #cell(created_at)="data">
                                    @{{ formatDate(data.item.created_at) }}
                                </template>

                                <template #cell(actions)="data">
                                    <b-button variant="info" size="sm" @click="viewApplication(data.item)">
                                        <i class="fas fa-eye"></i>
                                    </b-button>
                                </template>

                                <template #table-busy>
                                    <div class="text-center my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>Cargando...</strong>
                                    </div>
                                </template>
                            </b-table>
                        </b-card-body>
                    </b-tab>
                </b-tabs>
            </b-card>
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
                        showApplyModal: false,
                        selectedJob: null,
                        recentJobs: [],
                        myApplications: [],
                        applicationForm: {
                            trabajo_id: '',
                            mensaje: '',
                            cv: null
                        },
                        errors: {},
                        jobFields: [
                            { key: 'titulo', label: 'Título', sortable: true },
                            { key: 'descripcion', label: 'Descripción', sortable: false },
                            { key: 'sueldo', label: 'Sueldo', sortable: true },
                            { key: 'created_at', label: 'Fecha', sortable: true },
                            { key: 'actions', label: 'Acciones', sortable: false }
                        ],
                        applicationFields: [
                            { key: 'trabajo.titulo', label: 'Trabajo', sortable: true },
                            { key: 'trabajo.sueldo', label: 'Sueldo', sortable: true },
                            { key: 'estado', label: 'Estado', sortable: true },
                            { key: 'created_at', label: 'Fecha Postulación', sortable: true },
                            { key: 'actions', label: 'Acciones', sortable: false }
                        ]
                    },
                    computed: {
                        availableJobs() {
                            return this.recentJobs.length;
                        },
                        myApplicationsCount() {
                            return this.myApplications.length;
                        },
                        pendingApplications() {
                            return this.myApplications.filter(app => app.estado === 'pendiente').length;
                        },
                        recentApplications() {
                            const weekAgo = new Date();
                            weekAgo.setDate(weekAgo.getDate() - 7);
                            return this.myApplications.filter(app => 
                                new Date(app.created_at) > weekAgo
                            ).length;
                        }
                    },
                    mounted() {
                        this.loadDashboardData();
                    },
                    methods: {
                        loadDashboardData() {
                            this.loading = true;
                            Promise.all([
                                this.loadRecentJobs(),
                                this.loadMyApplications()
                            ]).finally(() => {
                                this.loading = false;
                            });
                        },
                        loadRecentJobs() {
                            return axios.get('/postulante/trabajos')
                                .then(response => {
                                    this.recentJobs = response.data.jobs.slice(0, 5); // Solo 5 más recientes
                                })
                                .catch(error => {
                                    this.$bvToast.toast('Error al cargar trabajos recientes', {
                                        title: 'Error',
                                        variant: 'danger',
                                        solid: true
                                    });
                                });
                        },
                        loadMyApplications() {
                            return axios.get('/postulante/dashboard')
                                .then(response => {
                                    this.myApplications = response.data.applications;
                                })
                                .catch(error => {
                                    this.$bvToast.toast('Error al cargar postulaciones', {
                                        title: 'Error',
                                        variant: 'danger',
                                        solid: true
                                    });
                                });
                        },
                        showJobs() {
                            window.location.href = '/postulante/trabajos';
                        },
                        showMyApplications() {
                            // Ya estamos en la pestaña de postulaciones
                        },
                        applyToJob(job) {
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
                                this.loadDashboardData();
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
                        getStatusVariant(status) {
                            const variants = {
                                'pendiente': 'warning',
                                'revisando': 'info',
                                'aceptada': 'success',
                                'rechazada': 'danger'
                            };
                            return variants[status] || 'secondary';
                        },
                        getStatusText(status) {
                            const texts = {
                                'pendiente': 'Pendiente',
                                'revisando': 'En Revisión',
                                'aceptada': 'Aceptada',
                                'rechazada': 'Rechazada'
                            };
                            return texts[status] || status;
                        },
                        viewApplication(application) {
                            // Implementar vista detallada
                            this.$bvToast.toast('Vista detallada en desarrollo', {
                                title: 'Info',
                                variant: 'info',
                                solid: true
                            });
                        },
                        refreshApplications() {
                            this.loadMyApplications();
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
</body>
</html> 