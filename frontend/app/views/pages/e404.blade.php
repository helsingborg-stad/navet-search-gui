@extends('layout.master')

@section('content')
    @segment([
    'title'             => 'Error 404',
    'content'           => 'Sidan som du sökte efter kan inte hittas.',
    'layout'            => 'full-width',
    'background'        => false,
    'textColor'         => 'primary',
    'overlay'           => 'dark',
    'textAlignment'     => 'center',
    'height'            => 'full-screen',
    ])
    @endsegment

    @if(isset($errorMessage))
        @notice(['classList' => ['u-fixed--bottom']])
            Ett fel inträffade på rad <strong>{{ $errorMessage['line'] }}</strong> i <strong>{{ $errorMessage['file'] }}</strong>.
        @endnotice()
    @endif
   
@endsection