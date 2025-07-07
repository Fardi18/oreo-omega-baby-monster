@if (count($errors) > 0)
    <!-- Alert Failed -->
    <div class="alert alert-mini alert-danger margin-bottom-10">
        <strong>{{ lang('Oops! Something went wrong...', $translation) }}</strong><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div><!-- /Alert Failed -->
@endif

@if (session('error'))
    <!-- Alert Failed -->
    <div class="alert alert-mini alert-danger margin-bottom-10">
        <strong>{{ lang('Oops! Something went wrong...', $translation) }}</strong><br>
        {{ session('error') }}
    </div><!-- /Alert Failed -->
@endif

@if (session('success'))
    <!-- Alert Success -->
    <div class="alert alert-mini alert-success margin-bottom-10">
        <strong>{{ lang('Yeah! Success...', $translation) }}</strong><br>
        {{ session('success') }}
    </div><!-- /Alert Success -->
@endif

@if (session('warning'))
    <!-- Alert Warning -->
    <div class="alert alert-mini alert-warning margin-bottom-10">
        <strong>{{ lang('Warning, need your attention a moment!', $translation) }}</strong><br>
        {{ session('warning') }}
    </div><!-- /Alert Warning -->
@endif

@if (session('info'))
    <!-- Alert Info -->
    <div class="alert alert-mini alert-info margin-bottom-10">
        <strong>{{ lang('Info', $translation) }}</strong><br>
        {{ session('info') }}
    </div><!-- /Alert Info -->
@endif