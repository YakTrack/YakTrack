<?php

it('can visit the homepage', function () {
    $page = visit('/')
        ->assertSee('YakTrack');
});

it('can test basic page interactions', function () {
    $page = visit('/login')
        ->assertSee('Log in to start your session')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Login');
});

it('can test form interactions', function () {
    $page = visit('/login')
        ->type('email', 'test@example.com')
        ->type('password', 'testpassword')
        ->assertSee('Email')
        ->assertSee('Password');
});

it('can test responsive design', function () {
    $page = visit('/login')
        ->on()
        ->mobile()
        ->assertSee('Log in to start your session')
        ->resize(1280, 720)
        ->assertSee('Log in to start your session');
});

it('can test multiple pages simultaneously', function () {
    $pages = visit(['/', '/login']);
    
    $pages->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertNoConsoleLogs()
        ->assertNoJavaScriptErrors();
    
    [$homePage, $loginPage] = $pages;
    
    $homePage->assertSee('YakTrack');
    $loginPage->assertSee('Log in to start your session');
});
