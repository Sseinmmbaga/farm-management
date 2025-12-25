<?php

namespace App\Forms;

abstract class FormTemplate
{
    // Form metadata
    protected string $formType;
    protected string $formSubtype;
    protected string $title;
    protected string $description;
    protected array $requiredFields = [];
    protected array $sections = [];

    // Get form definition
    abstract public function getDefinition(): array;

    // Get form type
    public function getFormType(): string
    {
        return $this->formType;
    }

    // Get form subtype
    public function getFormSubtype(): string
    {
        return $this->formSubtype;
    }

    // Get form title
    public function getTitle(): string
    {
        return $this->title;
    }

    // Get form description
    public function getDescription(): string
    {
        return $this->description;
    }

    // Get required fields
    public function getRequiredFields(): array
    {
        return $this->requiredFields;
    }

    // Get sections
    public function getSections(): array
    {
        return $this->sections;
    }

    // Validate form data
    public function validate(array $data): array
    {
        $errors = [];

        // Check required fields
        foreach ($this->requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = 'This field is required';
            }
        }

        // Additional validation rules can be added by child classes
        return $errors;
    }

    // Process form data before saving
    public function processData(array $data): array
    {
        return $data;
    }

    // Get default values for the form
    public function getDefaultValues(): array
    {
        return [];
    }

    // Helper method to create field definition
    protected function createField(
        string $name,
        string $label,
        string $type = 'text',
        array $options = [],
        bool $required = false,
        ?string $description = null
    ): array {
        $field = [
            'name' => $name,
            'label' => $label,
            'type' => $type,
            'options' => $options,
            'required' => $required,
        ];

        if ($description) {
            $field['description'] = $description;
        }

        if ($required) {
            $this->requiredFields[] = $name;
        }

        return $field;
    }

    // Helper method to create section
    protected function createSection(
        string $name,
        string $title,
        ?string $description = null,
        array $fields = [],
        int $order = 0
    ): array {
        $section = [
            'name' => $name,
            'title' => $title,
            'order' => $order,
            'fields' => $fields,
        ];

        if ($description) {
            $section['description'] = $description;
        }

        $this->sections[$name] = $section;
        return $section;
    }
}
