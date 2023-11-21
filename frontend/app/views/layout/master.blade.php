<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <title>Navet Personuppslag</title>

    @if($assets) 
        @foreach($assets as $assetKey => $asset)
            <link rel='stylesheet' id='{{$assetKey}}' href='/assets/dist/{{$asset}}' media='all'>
        @endforeach
    @endif

    <style type="text/css">
        @font-face {
          font-family: 'Material Icons';
          font-style: normal;
          font-weight: 400;
          src: url('https://helsingborg.se/wp-content/fonts/material-icons/flUhRq6tzZclQEJ-Vdg-IuiaDsNaIhQ8tQ.woff') format('woff');
        }
        
        .material-icons {
          font-family: 'Material Icons';
          font-weight: normal;
          font-style: normal;
          font-size: 24px;
          line-height: 1;
          letter-spacing: normal;
          text-transform: none;
          display: inline-block;
          white-space: nowrap;
          word-wrap: normal;
          direction: ltr;
          text-rendering: optimizeLegibility;
          -webkit-font-smoothing: antialiased;
        }
    </style>

    <style type="text/css">
        {{-- Themes may be copied from any Munipio site. --}}
        @include('themes.helsingborg')
    </style>

</head>
<body class="no-js">
    {{-- App not built correctly message. --}}
    @if(!$assets) 
        @include('notices.assets')
    @endif

    {{-- Main content --}}
    @yield('content')

    <!-- Logout -->
    @if($isAuthenticated)
        @button([
            'icon' => 'logout',
            'size' => 'md',
            'color' => 'primary',
            'text' => 'Logga ut',
            'reversePositions' => true,
            'href' => '/?action=logout',
            'classList' => ['u-fixed--bottom-right', 'u-margin--2']
        ])
        @endbutton
    @endif

    <footer class="u-display--flex u-align-content--center u-flex-direction--column">
        @logotype([
            'src'=> "/assets/img/logotype-grey.svg",
            'alt' => "Logotype for Helsingborg Stad",
            'attributeList' => [
                'style' => 'opacity: .4;'
            ],
            'classList' => [
                'u-align-self--center',
                'u-padding--4',
                'u-padding__bottom--2'
            ]
        ])
        @endlogotype

        @typography([
            'element' => 'span',
            'variant' => 'meta',
            'classList' => [
                'u-align-self--center',
                'u-padding--0',
                'u-color__text--light',
                'u-text-align--center'
            ]
        ])
            När du använder den här tjänsten <br/>accepterar du <a href="https://helsingborg.se/toppmeny/om-webbplatsen/sa-har-behandlar-vi-dina-personuppgifter/" target="_blank">Helsingborg Stads datapolicy</a>. 
        @endtypography
    </footer>
</body>
</html>
