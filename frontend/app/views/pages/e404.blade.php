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

    <?php var_dump($errorMessage); ?> 
@endsection