<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar Sesión - Job Finder</title>

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
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow">
                            <div class="card-body p-5">
                                <div class="text-center mb-4">
                                    <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
                                    <h2 class="h3">Iniciar Sesión</h2>
                                    <p class="text-muted">Accede a tu cuenta</p>
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

                                <b-form @submit.prevent="onSubmit">
                                    @csrf
                                    
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
                                            El correo electrónico es obligatorio.
                                        </b-form-invalid-feedback>
                                    </b-form-group>

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
                                            La contraseña es obligatoria.
                                        </b-form-invalid-feedback>
                                    </b-form-group>

                                    <b-form-group>
                                        <b-form-checkbox
                                            id="remember"
                                            name="remember"
                                            v-model="form.remember">
                                            Recordarme
                                        </b-form-checkbox>
                                    </b-form-group>

                                    <b-button type="submit" variant="primary" block :disabled="loading">
                                        <b-spinner small v-if="loading"></b-spinner>
                                        <i class="fas fa-sign-in-alt mr-1" v-else></i>
                                        <span v-text="loading ? 'Iniciando sesión...' : 'Iniciar Sesión'"></span>
                                    </b-button>
                                </b-form>

                                <hr class="my-4">

                                <div class="text-center">
                                    <p class="mb-0">
                                        ¿No tienes cuenta? 
                                        <a href="{{ route('register') }}" class="text-primary">
                                            <i class="fas fa-user-plus mr-1"></i>
                                            Regístrate aquí
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
                            email: '',
                            password: '',
                            remember: false
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

                            axios.post('{{ route("login") }}', this.form)
                                .then(response => {
                                    if (response.data.success) {
                                        window.location.href = response.data.redirect || '/';
                                    }
                                })
                                .catch(error => {
                                    if (error.response && error.response.data.errors) {
                                        this.errors = error.response.data.errors;
                                    } else {
                                        this.$bvToast.toast('Credenciales inválidas. Por favor, verifica tu correo y contraseña.', {
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