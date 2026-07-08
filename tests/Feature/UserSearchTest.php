<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);

        $this->app['db']->purge('sqlite');

        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('designation')->nullable();
            $table->timestamps();
        });
    }

    public function test_search_matches_first_name(): void
    {
        User::create([
            'email' => 'alice@example.com',
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'designation' => 'Lecturer',
        ]);

        User::create([
            'email' => 'bob@example.com',
            'first_name' => 'Bob',
            'last_name' => 'Smith',
            'designation' => 'Admin',
        ]);

        $results = User::searchUsers('Ali')->pluck('email')->all();

        $this->assertContains('alice@example.com', $results);
        $this->assertNotContains('bob@example.com', $results);
    }
}
