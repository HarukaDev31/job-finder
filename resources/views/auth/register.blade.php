<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Registro - Job Finder</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <div class="min-h-screen bg-light d-flex align-items-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card shadow">
                            <div class="card-body p-5">
                                <div class="text-center mb-4">
                                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                                    <h2 class="h3">Registro de Postulante</h2>
                                    <p class="text-muted">Crea tu cuenta para acceder a las ofertas de trabajo</p>
                                </div>

                                @if ($errors->any())
                                    <b-alert variant="danger" show>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </b-alert>
                                @endif

                                <b-form @submit.prevent="onSubmit" method="POST" action="{{ route('register') }}">
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b-form-group label="Nombres:" label-for="nombres">
                                                <b-form-input
                                                    id="nombres"
                                                    name="nombres"
                                                    v-model="form.nombres"
                                                    :state="validateState('nombres')"
                                                    required
                                                    placeholder="Ingresa tus nombres">
                                                </b-form-input>
                                                <b-form-invalid-feedback :state="validateState('nombres')">
                                                    Los nombres son obligatorios.
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                        </div>
                                        <div class="col-md-6">
                                            <b-form-group label="Apellidos:" label-for="apellidos">
                                                <b-form-input
                                                    id="apellidos"
                                                    name="apellidos"
                                                    v-model="form.apellidos"
                                                    :state="validateState('apellidos')"
                                                    required
                                                    placeholder="Ingresa tus apellidos">
                                                </b-form-input>
                                                <b-form-invalid-feedback :state="validateState('apellidos')">
                                                    Los apellidos son obligatorios.
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <b-form-group label="Tipo de Documento:" label-for="tipo_documento">
                                                <b-form-select
                                                    id="tipo_documento"
                                                    name="tipo_documento"
                                                    v-model="form.tipo_documento"
                                                    :state="validateState('tipo_documento')"
                                                    required>
                                                    <option value="">Selecciona un tipo</option>
                                                    <option value="CC">Cédula de Ciudadanía</option>
                                                    <option value="CE">Cédula de Extranjería</option>
                                                    <option value="TI">Tarjeta de Identidad</option>
                                                    <option value="PP">Pasaporte</option>
                                                </b-form-select>
                                                <b-form-invalid-feedback :state="validateState('tipo_documento')">
                                                    El tipo de documento es obligatorio.
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                        </div>
                                        <div class="col-md-6">
                                            <b-form-group label="Número de Documento:" label-for="numero_documento">
                                                <b-form-input
                                                    id="numero_documento"
                                                    name="numero_documento"
                                                    v-model="form.numero_documento"
                                                    :state="validateState('numero_documento')"
                                                    required
                                                    placeholder="Ingresa tu número de documento">
                                                </b-form-input>
                                                <b-form-invalid-feedback :state="validateState('numero_documento')">
                                                    El número de documento es obligatorio.
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <b-form-group label="Fecha de Nacimiento:" label-for="fecha_nacimiento">
                                                <b-form-input
                                                    id="fecha_nacimiento"
                                                    name="fecha_nacimiento"
                                                    v-model="form.fecha_nacimiento"
                                                    :state="validateState('fecha_nacimiento')"
                                                    type="date"
                                                    required>
                                                </b-form-input>
                                                <b-form-invalid-feedback :state="validateState('fecha_nacimiento')">
                                                    La fecha de nacimiento es obligatoria.
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                        </div>
                                        <div class="col-md-6">
                                            <b-form-group label="Correo Electrónico:" label-for="email">
                                                <b-form-input
                                                    id="email"
                                                    name="email"
                                                    v-model="form.email"
                                                    :state="validateState('email')"
                                                    type="email"
                                                    required
                                                    placeholder="Ingresa tu correo electrónico">
                                                </b-form-input>
                                                <b-form-invalid-feedback :state="validateState('email')">
                                                    El correo electrónico es obligatorio y debe ser válido.
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <b-form-group label="Contraseña:" label-for="password">
                                                <b-form-input
                                                    id="password"
                                                    name="password"
                                                    v-model="form.password"
                                                    :state="validateState('password')"
                                                    type="password"
                                                    required
                                                    placeholder="Ingresa tu contraseña">
                                                </b-form-input>
                                                <b-form-invalid-feedback :state="validateState('password')">
                                                    La contraseña debe tener al menos 8 caracteres.
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                        </div>
                                        <div class="col-md-6">
                                            <b-form-group label="Confirmar Contraseña:" label-for="password_confirmation">
                                                <b-form-input
                                                    id="password_confirmation"
                                                    name="password_confirmation"
                                                    v-model="form.password_confirmation"
                                                    :state="validateState('password_confirmation')"
                                                    type="password"
                                                    required
                                                    placeholder="Confirma tu contraseña">
                                                </b-form-input>
                                                <b-form-invalid-feedback :state="validateState('password_confirmation')">
                                                    Las contraseñas no coinciden.
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                        </div>
                                    </div>

                                    <b-button type="submit" variant="primary" block :disabled="loading">
                                        <b-spinner small v-if="loading"></b-spinner>
                                        <i class="fas fa-user-plus mr-1" v-else></i>
                                        <span v-text="loading ? 'Registrando...' : 'Registrarse'"></span>
                                    </b-button>
                                </b-form>

                                <hr class="my-4">

                                <div class="text-center">
                                    <p class="mb-0">
                                        ¿Ya tienes cuenta? 
                                        <a href="{{ route('login') }}" class="text-primary">
                                            <i class="fas fa-sign-in-alt mr-1"></i>
                                            Inicia sesión aquí
                                        </a>
                                    </p>
                                </div>

                                <div class="text-center mt-3">
                                    <a href="/" class="text-muted">
                                        <i class="fas fa-arrow-left mr-1"></i>
                                        Volver al inicio
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Esperar a que Vue esté disponible
        function initVue() {
            if (typeof Vue !== 'undefined') {
                new Vue({
                    el: '#app',
                    data: {
                        form: {
                            nombres: '',
                            apellidos: '',
                            tipo_documento: '',
                            numero_documento: '',
                            fecha_nacimiento: '',
                            email: '',
                            password: '',
                            password_confirmation: ''
                        },
                        loading: false,
                        errors: {}
                    },
                    methods: {
                        validateState: function(field) {
                            if (this.errors[field]) {
                                return false;
                            }
                            return null;
                        },
                        onSubmit: function() {
                            this.loading = true;
                            this.errors = {};

                            axios.post('{{ route("register") }}', this.form)
                                .then(response => {
                                    if (response.data.success) {
                                        window.location.href = '/postulante/dashboard';
                                    }
                                })
                                .catch(error => {
                                    if (error.response && error.response.data.errors) {
                                        this.errors = error.response.data.errors;
                                    } else {
                                        this.$bvToast.toast('Error al registrar. Por favor, intente nuevamente.', {
                                            title: 'Error',
                                            variant: 'danger',
                                            solid: true
                                        });
                                    }
                                })
                                .finally(() => {
                                    this.loading = false;
                                });
                        }
                    }
                });
            } else {
                // Reintentar después de un breve delay
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