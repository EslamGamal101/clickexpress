<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_as_customer()
    {
        $data = [
            'phone'      => '791234567',
            'password'   => 'Test@123',
            'type'       => 'customer',
            'first_name' => 'أحمد',
            'last_name'  => 'خالد',
            'city'       => 'عمان',
            'area'       => 'شارع الجامعة',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'msg',
                'data' => [
                    'user' => [
                        'id',
                        'phone',
                        'type',
                        'profile',
                    ],
                    'token',
                ]
            ]);
    }

    /** @test */
    public function it_requires_valid_password()
    {
        $data = [
            'phone'    => '791234567',
            'password' => '123', // كلمة مرور ضعيفة
            'type'     => 'customer',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function it_requires_valid_phone_number()
    {
        $data = [
            'phone'    => '012345678', // خطأ لأنه يبدأ بـ 0
            'password' => 'Test@123',
            'type'     => 'customer',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }
}
