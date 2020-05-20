@if (count($breadcrumbs))

    <nav class="font-sans w-full pb-4">
        <ol class="list-reset flex text-gray-600">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <li class="">
                        <a class="text-gray-500 text-light no-underline" href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                        <span class="mx-4 text-gray-300"> > </span>
                    </li>
                @else
                    <li class="">{{ $breadcrumb->title }}</li>
                @endif

            @endforeach
        </ol>
    </nav>

@endif
