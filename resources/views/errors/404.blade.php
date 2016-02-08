@extends ('layouts.master')
@section ('title', app('translator')->get('ship.errors.not-found.title'))

@section ('content')
<h1>@lang('ship.errors.not-found.header')</h1>
@if ($message = $exception->getMessage())
<pre><code>{{ $message }}</code></pre>
@endif
<p><a href="/">@lang('ship.errors.not-found.link')</a></p>
@endsection
