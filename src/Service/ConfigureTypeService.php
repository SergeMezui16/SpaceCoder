<?php
namespace App\Service;

use Symfony\Component\Validator\Constraints\NotBlank;

class ConfigureTypeService 
{
    /**
     * Default size of TextType input
     *
     * @var array
     */
    public $sizeConstraint = ['minlength' => 3, 'maxlength' => 50];


    protected array $class = [
        'FORM_INPUT' => 'form-input',
        'SELECT_INPUT' => 'form-select-date',
        'HELP_TEXT' => 'help-text',
        'ERROR' => 'error-text'
    ];

    /**
     * Set the configuration of input field
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    public function setConfiguration(
        string $label, 
        string $placeholder = '',
        bool $required = false,
        string $help = '',
        array $options = []
    ): array
    {
        return [
            'label' => $label,
            'help' => $help,
            'required' => $required,
            'attr' => [
                ...$options,
                ...($required ? ['required' => 'required'] : []),
                'placeholder' => $placeholder,
                'class' => $this->class['FORM_INPUT'],
            ]
        ];
    }

    public function resetPasswordEmail(
        string $label,
        string $placeholder = '',
        string $help = '',
        array $options = []
    ): array {
        return [
            'label' => $label,
            'help' => $help,
            'required' => true,
            'attr' => [
                ...$options,
                'autocomplete' => 'email',
                'placeholder' => $placeholder,
                'class' => $this->class['FORM_INPUT'],
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez enter votre email.',
                ]),
            ],
        ];
    }

    public function avatar(
        string $label, 
        string $placeholder = '',
        bool $required = false,
        string $help = '',
        array $options = []
    ): array
    {
        return [
            'label' => $label,
            'help' => $help,
            'required' => $required,
            'data_class' => null,
            'attr' => [
                ...$options,
                ...($required ? ['required' => 'required'] : []),
                'placeholder' => $placeholder,
                'class' => $this->class['FORM_INPUT'],
            ]
        ];
    }

    public function select(
        string $label, 
        string $placeholder = '',
        bool $required = false,
        string $help = '',
        array $options = []
    ): array
    {
        return [
            'label' => $label,
            'help' => $help,
            'placeholder' => $placeholder,
            'required' => $required,
            'attr' => [
                ...$options,
                'class' => $this->class['FORM_INPUT'],
            ]
        ];
    }


    public function setDateConfiguration(string $label, bool $required, array $options = []): array
    {
        return [
            // "action", 
            // "allow_extra_fields", 
            // "allow_file_upload", 
            // "attr_translation_parameters", 
            // "auto_initialize", 
            // "block_name", 
            // "block_prefix", 
            // "by_reference", 
            // "choice_translation_domain", 
            // "compound", 
            // "constraints", 
            // "csrf_field_name", 
            // "csrf_message", 
            // "csrf_protection", 
            // "csrf_token_id", 
            // "csrf_token_manager", 
            // "data", 
            // "data_class", 
            // "days", 
            // "disabled", 
            // "ea_crud_form", 
            // "empty_data", 
            // "error_bubbling", 
            // "error_mapping", 
            // "extra_fields_message", 
            // "form_attr", 
            // "format", 
            // "getter", 
            // "help", 
            // "help_attr", 
            // "help_html", 
            // "help_translation_parameters", 
            // "html5", 
            // "inherit_data", 
            'input' => 'datetime_immutable', 
            'input_format' => 'Y-m-d', 
            // "invalid_message", 
            // "invalid_message_parameters", 
            // "is_empty_callback", 
            // "label", 
            // "label_attr", 
            // "label_format", 
            // "label_html", 
            // "label_translation_parameters", 
            // "mapped", 
            // "method", 
            // "model_timezone", 
            // "months", 
            // "placeholder", 
            // "post_max_size_message", 
            // "priority", 
            // "property_path", 
            // "row_attr", 
            // "setter", 
            // "translation_domain", 
            // "trim", 
            // "upload_max_size_message", 
            // "validation_groups", 
            // "view_timezone", 
            // 'with_minutes' => false,
            // 'with_seconds' => false,
            'required' => $required,
            'years' => $this->getYears(),
            'label' => $label,
            'widget' => 'choice',
            'attr' => [
                ...$options,
                'class' => $this->class['SELECT_INPUT'],
            ]
        ];
    }

     /**
     * Set size of input
     *
     * @param integer $min
     * @param integer $max
     * @return array
     */
    public function setSize(int $min, int $max): array
    {
        return [
            'minlength' => $min, 
            'maxlength' => $max
        ];
    }

    /**
     * Get the years available to intenet
     *
     * @return array
     */
    protected function getYears(): array
    {
        return range(1900, (date('Y') - 10), 1);
    }
}