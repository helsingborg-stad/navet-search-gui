@typography(['element' => 'p'])
  Ange personnumret på den person som du vill göra uppslag på.
  Du får bara göra uppslag på personer som du behöver ha information om i ditt arbete.
@endtypography

<div class="u-display--flex u-flex-direction--column u-flex--gridgap">
    
    @includeIf('notices.' . $action)

    @form([
        'method' => 'POST',
        'action' => '/sok/?action=sok',
        'classList' => ['u-margin__top--2']
    ])
        <div class="u-display--flex u-flex-direction--column u-flex--gridgap">
            @field([
                'type' => 'number',
                'name' => 'pnr',
                'label' => "Personnummer",
                'required' => true,
                'placeholder' => "T.ex: 1900000000",
                'value' => isset($_GET['pnr']) ? $_GET['pnr'] : '194107086995',
                'helperText' => "Notera att samtliga uppslag som du (" . $user->displayname . ") gör registreras.",
                'attributeList' => [
                    'maxlength' => '12',
                    'minlength' => '12'
                ]
            ])
            @endfield

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
    @endform
</div>