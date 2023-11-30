<?php $code = isset($_GET['code']) ? (int) $_GET['code'] : false; ?>

@if($code == 429)
    @notice([
        'type' => 'info',
        'message' => [
            'text' => 'Antalet sökningar har överskridits, vänligen avvakta några minuter och försök igen.',
            'size' => 'sm'
        ],
        'icon' => [
            'name' => 'report',
            'size' => 'md',
            'color' => 'white'
        ],
        'classList' => ['u-margin__top--2']
    ])
    @endnotice
@elseif($code == 500)
    @notice([
        'type' => 'info',
        'message' => [
            'text' => 'Ett okänt fel inträffade.',
            'size' => 'sm'
        ],
        'icon' => [
            'name' => 'report',
            'size' => 'md',
            'color' => 'white'
        ],
        'classList' => ['u-margin__top--2']
    ])
    @endnotice
@else
    @notice([
        'type' => 'info',
        'message' => [
            'text' => 'Vi kunde inte hitta någon information på angivet personnummer.',
            'size' => 'sm'
        ],
        'icon' => [
            'name' => 'report',
            'size' => 'md',
            'color' => 'white'
        ],
        'classList' => ['u-margin__top--2']
    ])
    @endnotice
@endif