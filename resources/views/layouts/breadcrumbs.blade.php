@if (count($breadcrumbs))

    <nav class="font-sans w-full pb-4">
        <ol class="list-reset flex text-grey-dark">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <li class="">
                        <a class="text-grey text-light no-underline" href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                        <span class="mx-4 text-grey-light"> > </span>
                    </li>
                @else
                    <li class="">{{ $breadcrumb->title }}</li>
                @endif

            @endforeach
        </ol>
    </nav>

@endif
