@props([
    'title' => '',
    'subtitle' => null,
    'breadcrumb' => [],
])

<header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">

    <div class="min-w-0">

        @if(!empty($breadcrumb))
            <p class="text-xs text-muted">
                @foreach($breadcrumb as $item)
                    @if(!$loop->first)
                        <span class="mx-1">›</span>
                    @endif

                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="hover:underline">{{ $item['label'] }}</a>
                    @else
                        <span class="text-navy font-semibold">{{ $item['label'] }}</span>
                    @endif
                @endforeach
            </p>
        @endif

        @if($title)
            <h1 class="text-xl font-extrabold text-navy {{ !empty($breadcrumb) ? 'mt-1' : '' }}">
                {{ $title }}
            </h1>
        @endif

        @if($subtitle)
            <p class="text-xs text-muted mt-0.5">{{ $subtitle }}</p>
        @endif

    </div>

</header>