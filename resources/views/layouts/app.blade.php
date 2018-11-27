@extends('layouts.blank')

@section('page')
    @include('layouts.header-nav')
    <div class="w-full max-w-5xl mx-auto px-6x">
        <div class="lg:flex -mx-6">
            @include('layouts.sidebar')
            <div id="content-wrapper" class="min-h-screen w-full lg:static lg:max-h-full lg:overflow-visible">
                <main role="main" class="lg:sticky w-full py-24">
                    @yield('breadcrumbs')
                    @include('layouts.messages')
                    <div class="flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                        <div class="flex-1">
                            <h1 class="h2 font-normal"> @yield('title') </h1>
                        </div>
                        <div class="btn-toolbar mb-2 mb-md-0 flex-1 text-right">
                            @yield('top-right-toolbar')
                        </div>
                    </div>
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
@endsection