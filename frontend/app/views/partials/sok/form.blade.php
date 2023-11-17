@typography(['element' => 'p'])
  Ange personnumret på den person som du vill göra uppslag på.
  Du får bara göra uppslag på personer som du behöver ha information om i ditt arbete.
@endtypography

@includeIf('notices.' . $action)

@form([
    'method' => 'POST',
    'action' => '/sok/?action=sok',
    'classList' => ['u-margin__top--2']
])
    <div class="o-grid o-grid--half-gutter u-margin__top--4">

        <div class="o-grid-12">
            @field([
                'type' => 'number',
                'name' => 'pnr',
                'label' => "Personnummer",
                'required' => true,
                'placeholder' => "T.ex: 1900000000",
                'value' => isset($_GET['pnr']) ? $_GET['pnr'] : '194107086995'
            ])
            @endfield

            @typography(['element' => 'p', 'variant' => 'meta'])
                <strong>Inloggad som:</strong> {{$user->displayname}}. Samtliga uppslag som du gör registreras för spårbarhet av uppgifter. 
            @endtypography
        </div>
    </div>

    <div class="o-grid o-grid--no-gutter">
        <div class="o-grid-12">
            @button([
                'text' => 'Sök',
                'color' => 'primary',
                'type' => 'basic',
                'classList' => [
                    'u-width--100'
                ]
            ])
            @endbutton
        </div>
    </div>
    
@endform

