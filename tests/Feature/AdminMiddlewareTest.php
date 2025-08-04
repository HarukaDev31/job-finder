<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_middleware_blocks_regular_users()
    {
        // Crear usuarios
        $adminUser = User::factory()->create(['role' => 'admin']);
        $regularUser = User::factory()->create(['role' => 'postulante']);
        
        // Test con usuario regular - debería ser bloqueado
        $response = $this->authenticatedRequest($regularUser, 'GET', '/api/admin/metrics');
        
        $this->assertEquals(403, $response->status());
        $this->assertJson($response->getContent());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Acceso denegado. Se requieren permisos de administrador.', $responseData['message']);
    }

    public function test_admin_middleware_allows_admin_users()
    {
        // Crear usuario admin
        $adminUser = User::factory()->create(['role' => 'admin']);
        
        // Test con usuario admin - debería ser permitido
        $response = $this->authenticatedRequest($adminUser, 'GET', '/api/admin/metrics');
        
        $this->assertEquals(200, $response->status());
    }

    public function test_admin_middleware_blocks_unauthenticated_users()
    {
        // Test sin autenticación - debería ser bloqueado
        $response = $this->json('GET', '/api/admin/metrics');
        
        $this->assertEquals(401, $response->status());
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
} 