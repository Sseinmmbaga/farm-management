<?php

namespace App\Forms;

class FormManager
{
    protected array $formTemplates = [];

    public function __construct()
    {
        $this->registerDefaultTemplates();
    }

    protected function registerDefaultTemplates(): void
    {
        $this->registerTemplate(TrainingAttendanceForm::class);
        $this->registerTemplate(ProductionActivityForm::class);
        // Additional templates will be registered here as they are created
    }

    public function registerTemplate(string $templateClass): void
    {
        if (!class_exists($templateClass)) {
            throw new \InvalidArgumentException("Form template class {$templateClass} does not exist");
        }

        if (!is_subclass_of($templateClass, FormTemplate::class)) {
            throw new \InvalidArgumentException("Form template must extend " . FormTemplate::class);
        }

        $template = new $templateClass();
        $key = $template->getFormType() . '.' . $template->getFormSubtype();
        $this->formTemplates[$key] = $template;
    }

    public function getTemplate(string $formType, string $formSubtype): ?FormTemplate
    {
        $key = $formType . '.' . $formSubtype;
        return $this->formTemplates[$key] ?? null;
    }

    public function getTemplateByKey(string $key): ?FormTemplate
    {
        return $this->formTemplates[$key] ?? null;
    }

    public function getAllTemplates(): array
    {
        return $this->formTemplates;
    }

    public function getTemplatesByType(string $formType): array
    {
        return array_filter($this->formTemplates, function ($template) use ($formType) {
            return $template->getFormType() === $formType;
        });
    }

    public function getAvailableFormTypes(): array
    {
        $types = [];
        foreach ($this->formTemplates as $template) {
            $type = $template->getFormType();
            if (!in_array($type, $types)) {
                $types[] = $type;
            }
        }
        return $types;
    }

    public function getFormTypeOptions(): array
    {
        $options = [];
        foreach ($this->getAvailableFormTypes() as $type) {
            $options[$type] = $this->getFormTypeLabel($type);
        }
        return $options;
    }

    public function getFormSubtypeOptions(string $formType): array
    {
        $options = [];
        foreach ($this->getTemplatesByType($formType) as $template) {
            $subtype = $template->getFormSubtype();
            $options[$subtype] = $template->getTitle();
        }
        return $options;
    }

    protected function getFormTypeLabel(string $formType): string
    {
        $labels = [
            'training' => 'Training Records',
            'production' => 'Production Activities',
            'inspection' => 'Inspections',
            'input_application' => 'Input Applications',
            'harvest' => 'Harvest Records',
            'soil_test' => 'Soil Testing',
            'irrigation' => 'Irrigation Records',
            'pest_disease' => 'Pest & Disease Monitoring',
            'weather' => 'Weather Observations',
            'equipment' => 'Equipment Records',
            'labor' => 'Labor Records',
            'certification' => 'Certification',
            'other' => 'Other Forms',
        ];

        return $labels[$formType] ?? ucfirst(str_replace('_', ' ', $formType));
    }

    public function validateFormData(string $formType, string $formSubtype, array $data): array
    {
        $template = $this->getTemplate($formType, $formSubtype);
        if (!$template) {
            throw new \InvalidArgumentException("Form template not found for {$formType}.{$formSubtype}");
        }

        return $template->validate($data);
    }

    public function processFormData(string $formType, string $formSubtype, array $data): array
    {
        $template = $this->getTemplate($formType, $formSubtype);
        if (!$template) {
            throw new \InvalidArgumentException("Form template not found for {$formType}.{$formSubtype}");
        }

        return $template->processData($data);
    }

    public function getFormDefinition(string $formType, string $formSubtype): array
    {
        $template = $this->getTemplate($formType, $formSubtype);
        if (!$template) {
            throw new \InvalidArgumentException("Form template not found for {$formType}.{$formSubtype}");
        }

        return $template->getDefinition();
    }

    public function getDefaultValues(string $formType, string $formSubtype): array
    {
        $template = $this->getTemplate($formType, $formSubtype);
        if (!$template) {
            throw new \InvalidArgumentException("Form template not found for {$formType}.{$formSubtype}");
        }

        return $template->getDefaultValues();
    }

    public function createFormInstance(string $formType, string $formSubtype): FormTemplate
    {
        $template = $this->getTemplate($formType, $formSubtype);
        if (!$template) {
            throw new \InvalidArgumentException("Form template not found for {$formType}.{$formSubtype}");
        }

        return clone $template;
    }
}
