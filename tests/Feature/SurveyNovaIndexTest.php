<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SurveyNovaIndexTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_query_count()
    {
        $user = factory(User::class)->create([
            'name'     => 'Master',
            'role'     => \App\UserRole::MASTER,
            'password' => bcrypt('secret')
        ]);
        DB::enableQueryLog();
        $this->actingAs($user)
            ->getJson('/nova-api/surveys', [
                "orderByDirection" => "desc",
                "page"             => "1",
            ])
            ->assertStatus(200);
        $this->assertTrue(5 > count(DB::getQueryLog()));
    }
}
