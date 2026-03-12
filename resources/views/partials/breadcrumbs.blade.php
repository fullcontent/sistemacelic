@if (count($breadcrumbs))
    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url && !$loop->last)
                <li>
                    <a href="{{ $breadcrumb->url }}">
                        @if(isset($breadcrumb->icon))
                            <i class="{{ $breadcrumb->icon }}"></i>
                        @endif
                        {!! $breadcrumb->title !!}
                    </a>
                </li>
            @else
                <li class="active">
                    @if(isset($breadcrumb->icon))
                        <i class="{{ $breadcrumb->icon }}"></i>
                    @endif
                    {!! $breadcrumb->title !!}
                </li>
            @endif
        @endforeach
    </ol>
@endif
