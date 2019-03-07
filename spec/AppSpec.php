<?php

use Illuminate\Contracts\Foundation\Application;

describe('Laravel context for kahlan specs', function () {
    // Let's (re)create the DB as starting point,
    // because it gets wiped at the end, when
    // we use database migrations wrappers.
    beforeEach(function () {
        $this->laravel->artisan('migrate:fresh');
    });


    /*
    |--------------------------------------------------------------------------
    | You are free to use literally any of the Laravel features, eg. helpers
    |--------------------------------------------------------------------------
    */
    it('creates laravel app', function () {
        expect(app())->toBeAnInstanceOf(Application::class);
    });

    it('provides application in kahlan instance scope - as $this->app', function () {
        expect($this->app)->toBe(app());
        expect($this->laravel->app)->toBe(app());
    });

    it('binds to the container', function () {
        expect(app())->toBe($this->app);
        $stub = ['name' => 'stub'];
        $this->app->bind('some_service', function () use ($stub) {
            return ['name' => 'stub'];
        });
        expect($this->app->make('some_service'))->toEqual($stub);
    });

    it('recreates application for each single test', function () {
        // If you're not familiar with `toThrow()` matcher see
        // @link http://kahlan.readthedocs.io/en/latest/matchers/#classic-matchers
        //
        // Basically you'd wrap the code expected to throw the exception
        // in a closure and make expectation on the closure.
        expect(function () {
            $this->app['some_service'];
        })->toThrow(new ReflectionException('Class some_service does not exist', -1));
    });

    /*
    |--------------------------------------------------------------------------
    | The only difference from the original Laravel's TestCase
    | is in that here you call crawler/assertion methods on
    | `$this->laravel` helper object rather than `$this`.
    |--------------------------------------------------------------------------
    */
    context('It provides the same testing API as laravel TestCase', function () {
        it('crawls & asserts', function () {
            expect(function() {
                $this->laravel->get('/')
                              ->assertSee('Laravel')
                              ->assertSee('Docs')
                              ->assertSee('Laracast')
                              ->assertSee('News')
                              ->assertSee('Blog')
                              ->assertSee('Nova')
                              ->assertSee('Forge')
                              ->assertSee('GitHub')
                              ->assertSuccessful();
            })->not->toThrow();
        });

        it('interacts with database', function () {
            factory(App\User::class)->create(['email' => 'test@email.com']);
            expect(function() {
                $this->laravel->assertDatabaseHas('users', ['email' => 'test@email.com']);
            })->not->toThrow();
        });

        it('interacts with session', function () {
            expect(function() {
                $this->laravel->withSession(['session_test' => 'working'])
                              ->get('/session-test')
                              ->assertSee('working')
                              ->assertSessionHas('session_test','working');
            })->not->toThrow();
        });

        it('interacts with app services', function () {
            expect(function() {
                $this->laravel->expectsEvents('event_one', 'event_two')
                              ->doesntExpectEvents('event_three');

                event('event_one');
                event('event_two');
            })->not->toThrow();
        });
    });

});
