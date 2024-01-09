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
                'placeholder' => "T.ex: 1900000000",
                'value' => isset($_GET['pnr']) ? $_GET['pnr'] : '',
                'helperText' => "Notera att samtliga uppslag som du (" . $user->displayname . ") gör registreras.",
                'attributeList' => [
                    'maxlength' => '17',
                    'minlength' => '17',
                    'autofocus' => 'autofocus'
                ]
            ])
            @endfield

            <script type="text/javascript">
                class PnrFormatting {
                    constructor(inputField) {
                        this.inputField = inputField;
                        this.setupEventListeners();
                    }
                    applyFormat() {
                        let value = this.inputField.value.replaceAll(/[^\d]/g, '');
                        value = value.replace(/^(\d{4})(\d{2})(\d{2})?(\d{0,4})?$/, (_, year, month, day, rest) => {
                            if (year && month && day && rest && rest.length >= 4) {
                                return `${year} ${month} ${day} - ${rest}`;
                            }
                            return `${year}${month ? ` ${month}` : ''}${day ? ` ${day}` : ''}${rest ? ` - ${rest}` : ''}`;
                        });
                        this.inputField.value = value.trim();
                    }

                    shouldApplyFormat(event) {
                        return event.inputType !== 'deleteContentBackward' && event.inputType !== 'deleteContentForward';
                    }

                    setupEventListeners() {
                        if (this.inputField) {
                            this.inputField.addEventListener('input', () => {
                                if(this.shouldApplyFormat(event)) {
                                    this.applyFormat();
                                }
                            });
                        }
                    }
                }
                
                const inputField = document.getElementById('input_pnr-search-field');
                if (inputField) {
                    new PnrFormatting(inputField);
                }
            </script>

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