<?php

use App\Models\Session;
use App\Models\User;

/**
 * Lists visible elements that still paint a pure white background, which is the
 * tell-tale sign of a light-only panel left behind when the OS is in dark mode.
 */
const WHITE_BACKGROUND_ELEMENTS_SCRIPT = <<<'JS'
    [...document.querySelectorAll('body *')]
        .filter((element) => element.offsetParent !== null || getComputedStyle(element).position === 'fixed')
        .filter((element) => getComputedStyle(element).backgroundColor === 'rgb(255, 255, 255)')
        .map((element) => element.tagName.toLowerCase() + '.' + [...element.classList].join('.'))
    JS;

it('renders guest pages with a dark background in dark mode', function (string $url) {
    $page = visit($url)->inDarkMode();

    $page->assertNoJavaScriptErrors();

    expect($page->script('getComputedStyle(document.body).backgroundColor'))->toBe('rgb(26, 32, 44)')
        ->and($page->script(WHITE_BACKGROUND_ELEMENTS_SCRIPT))->toBe([]);
})->with([
    'login'              => '/login',
    'client portal login' => '/client-portal/login',
]);

it('renders authenticated pages with a dark background in dark mode', function (string $url) {
    $this->actingAs(User::factory()->create());
    Session::factory()->count(3)->create();

    $page = visit($url)->inDarkMode();

    $page->assertNoJavaScriptErrors();

    expect($page->script('getComputedStyle(document.body).backgroundColor'))->toBe('rgb(26, 32, 44)')
        ->and($page->script(WHITE_BACKGROUND_ELEMENTS_SCRIPT))->toBe([]);
})->with([
    'home'                => '/',
    'sessions'            => '/session',
    'create session'      => '/session/create',
    'tasks'               => '/task',
    'create task'         => '/task/create',
    'projects'            => '/project',
    'sprints'             => '/sprint',
    'clients'             => '/client',
    'invoices'            => '/invoice',
    'features'            => '/features',
    'acceptance criteria' => '/acceptance-criteria',
    'test runs'           => '/test-run',
    'task statuses'       => '/task-status',
    'session categories'  => '/session-category',
    'targets'             => '/target',
    'client users'        => '/client-users',
    'api tokens'          => '/api-tokens',
]);

it('keeps the light background outside dark mode', function () {
    $page = visit('/login')->inLightMode();

    expect($page->script('getComputedStyle(document.body).backgroundColor'))->toBe('rgb(247, 250, 252)');
});
