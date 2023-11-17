@extends('layout.containers.page')
@section('article')
    @typography([
        'element' => 'h1',
        'classList' => ['u-color__text--primary', 'u-margin__bottom--2']
    ])
        @icon(['icon' => 'person_search', 'size' => 'xl'])
        @endicon
        Sök person
    @endtypography

    @includeWhen(!isset($searchResult), 'partials.sok.form')
    @includeWhen(isset($searchResult), 'partials.sok.result')
@endsection