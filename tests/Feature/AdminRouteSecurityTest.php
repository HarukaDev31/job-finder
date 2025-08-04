<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Trabajo;
use App\Models\Postulacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminRouteSecurityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $regularUser;
    protected $job;
    protected $application;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ejecutar seeders para tener datos de prueba
        $this->seed([
            \Database\Seeders\AdminSeeder::class,
            \Database\Seeders\PostulanteSeeder::class,
            \Database\Seeders\TrabajoSeeder::class,
            \Database\Seeders\PostulacionSeeder::class,
        ]);
        
        // Obtener usuarios del seeder
        $this->adminUser = User::where('role', 'admin')->first();
        $this->regularUser = User::where('role', 'postulante')->first();
        
        // Obtener datos de prueba
        $this->job = Trabajo::first();
        $this->application = Postulacion::first();
    }

    protected function getJwtToken(User $user): string
    {
        return JWTAuth::fromUser($user);
    }

    protected function authenticatedRequest(User $user, string $method, string $url, array $data = []): \Illuminate\Testing\TestResponse
    {
        $token = $this->getJwtToken($user);
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json($method, $url, $data);
    }

    public function test_unauthenticated_user_cannot_access_admin_routes()
    {
        $adminRoutes = [
            'GET' => [
                '/api/admin/jobs',
                '/api/admin/applications',
                '/api/admin/metrics',
                "/api/admin/jobs/{$this->job->id}/applications",
                '/api/admin/applications/stats'
            ],
            'POST' => [
                '/api/admin/jobs'
            ],
            'PUT' => [
                "/api/admin/jobs/{$this->job->id}",
                "/api/admin/applications/{$this->application->id}/status"
            ],
            'DELETE' => [
                "/api/admin/jobs/{$this->job->id}"
            ]
        ];

        foreach ($adminRoutes as $method => $routes) {
            foreach ($routes as $route) {
                $response = $this->json($method, $route);
                
                $response->assertStatus(401)
                    ->assertJson([
                        'message' => 'Unauthenticated.'
                    ]);
            }
        }
    }

    public function test_regular_user_cannot_access_admin_routes()
    {
        $adminRoutes = [
            'GET' => [
                '/api/admin/jobs',
                '/api/admin/applications',
                '/api/admin/metrics',
                "/api/admin/jobs/{$this->job->id}/applications",
                '/api/admin/applications/stats'
            ],
            'POST' => [
                '/api/admin/jobs'
            ],
            'PUT' => [
                "/api/admin/jobs/{$this->job->id}",
                "/api/admin/applications/{$this->application->id}/status"
            ],
            'DELETE' => [
                "/api/admin/jobs/{$this->job->id}"
            ]
        ];

        foreach ($adminRoutes as $method => $routes) {
            foreach ($routes as $route) {
                $response = $this->authenticatedRequest($this->regularUser, $method, $route);
                
                $response->assertStatus(403)
                    ->assertJson([
                        'success' => false,
                        'message' => 'Acceso denegado. Se requieren permisos de administrador.',
                        'error' => 'INSUFFICIENT_PERMISSIONS'
                    ]);
            }
        }
    }

    public function test_admin_user_can_access_admin_routes()
    {
        $adminRoutes = [
            'GET' => [
                '/api/admin/jobs',
                '/api/admin/applications',
                '/api/admin/metrics',
                "/api/admin/jobs/{$this->job->id}/applications",
                '/api/admin/applications/stats'
            ]
        ];

        foreach ($adminRoutes as $method => $routes) {
            foreach ($routes as $route) {
                $response = $this->authenticatedRequest($this->adminUser, $method, $route);
                
                // Debería devolver 200 o 404 (si no hay datos), pero no 401 o 403
                $this->assertNotEquals(401, $response->status());
                $this->assertNotEquals(403, $response->status());
            }
        }
    }

    public function test_regular_user_cannot_create_jobs()
    {
        $jobData = [
            'titulo' => 'Nuevo Trabajo',
            'descripcion' => 'Descripción del nuevo trabajo',
            'sueldo' => 60000
        ];

        $response = $this->authenticatedRequest($this->regularUser, 'POST', '/api/admin/jobs', $jobData);
        
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Acceso denegado. Se requieren permisos de administrador.',
                'error' => 'INSUFFICIENT_PERMISSIONS'
            ]);
    }

    public function test_regular_user_cannot_update_jobs()
    {
        $updateData = [
            'titulo' => 'Trabajo Actualizado',
            'descripcion' => 'Descripción actualizada',
            'sueldo' => 70000
        ];

        $response = $this->authenticatedRequest($this->regularUser, 'PUT', "/api/admin/jobs/{$this->job->id}", $updateData);
        
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Acceso denegado. Se requieren permisos de administrador.',
                'error' => 'INSUFFICIENT_PERMISSIONS'
            ]);
    }

    public function test_regular_user_cannot_delete_jobs()
    {
        $response = $this->authenticatedRequest($this->regularUser, 'DELETE', "/api/admin/jobs/{$this->job->id}");
        
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Acceso denegado. Se requieren permisos de administrador.',
                'error' => 'INSUFFICIENT_PERMISSIONS'
            ]);
    }

    public function test_regular_user_cannot_update_application_status()
    {
        $statusData = [
            'estado' => 'aceptado'
        ];

        $response = $this->authenticatedRequest($this->regularUser, 'PUT', "/api/admin/applications/{$this->application->id}/status", $statusData);
        
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Acceso denegado. Se requieren permisos de administrador.',
                'error' => 'INSUFFICIENT_PERMISSIONS'
            ]);
    }



    public function test_public_routes_remain_accessible()
    {
        $publicRoutes = [
            'GET' => [
                '/api/stats',
                '/api/jobs/recent'
            ]
        ];

        foreach ($publicRoutes as $method => $routes) {
            foreach ($routes as $route) {
                $response = $this->json($method, $route);
                
                // Debería devolver 200 o 404, pero no 401 o 403
                $this->assertNotEquals(401, $response->status());
                $this->assertNotEquals(403, $response->status());
            }
        }
    }

    public function test_authenticated_user_routes_work_correctly()
    {
        $userRoutes = [
            'GET' => [
                '/api/jobs',
                '/api/applications/my'
            ]
        ];

        foreach ($userRoutes as $method => $routes) {
            foreach ($routes as $route) {
                $response = $this->authenticatedRequest($this->regularUser, $method, $route);
                
                // Debería devolver 200 o 404, pero no 401 o 403
                $this->assertNotEquals(401, $response->status());
                $this->assertNotEquals(403, $response->status());
            }
        }
    }
} 