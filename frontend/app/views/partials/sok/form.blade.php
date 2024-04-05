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
        'id' => 'pnr-search-field',
        'type' => 'text',
        'name' => 'pnr',
        'label' => "Personnummer",
        'required' => true,
        'placeholder' => "T.ex: 19000000-0000",
        'value' => isset($_GET['pnr']) ? $_GET['pnr'] : '',
        'helperText' => "Notera att samtliga uppslag som du (" . $user->getDisplayName() . ") gör registreras.",
        'attributeList' => [
        'maxlength' => '13',
        'minlength' => '13',
        'autofocus' => 'autofocus'
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