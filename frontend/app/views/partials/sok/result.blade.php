<div class="u-display--flex u-flex-direction--column u-flex--gridgap">
    
    @includeIf('notices.' . $action)

    @if($readableResult)
        @typography(['element' => 'p', 'classList' => ['u-margin__top--0']])
            {{ $readableResult }}
        @endtypography
    @endif

    @table([
        'title'         => "Personuppgifter",
        'headings'      => false,
        'showHeader'    => false,
        'list'          => $basicData
    ])
    @endtable

    @table([
        'title'         => "Adress",
        'headings'      => false,
        'showHeader'    => false,
        'list'          => $adressData
    ])
    @endtable

    @button([
        'text' => 'Tillbaka till sök',
        'href' => '/sok',
        'color' => 'default',
        'style' => 'filled',
        'classList' => [
            'u-width--100',
        ]
    ])
    @endbutton

</div>