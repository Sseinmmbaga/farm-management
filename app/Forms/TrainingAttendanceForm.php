<?php

namespace App\Forms;

class TrainingAttendanceForm extends FormTemplate
{
    public function __construct()
    {
        $this->formType = 'training';
        $this->formSubtype = 'attendance';
        $this->title = 'Training Attendance Record';
        $this->description = 'Record attendance and details of training sessions';
    }

    public function getDefinition(): array
    {
        return [
            'form_type' => $this->formType,
            'form_subtype' => $this->formSubtype,
            'title' => $this->title,
            'description' => $this->description,
            'sections' => $this->getSections(),
            'required_fields' => $this->requiredFields,
        ];
    }

    public function getSections(): array
    {
        // Clear any existing sections
        $this->sections = [];
        
        // Training Details Section
        $this->createSection(
            'training_details',
            'Training Details',
            'Basic information about the training session',
            [
                $this->createField(
                    'training_topic',
                    'Training Topic',
                    'text',
                    [],
                    true,
                    'Topic or title of the training session'
                ),
                $this->createField(
                    'trainer_name',
                    'Trainer Name',
                    'text',
                    [],
                    true,
                    'Name of the trainer/facilitator'
                ),
                $this->createField(
                    'training_date',
                    'Training Date',
                    'date',
                    [],
                    true,
                    'Date when training was conducted'
                ),
                $this->createField(
                    'start_time',
                    'Start Time',
                    'time',
                    [],
                    false,
                    'Training start time'
                ),
                $this->createField(
                    'end_time',
                    'End Time',
                    'time',
                    [],
                    false,
                    'Training end time'
                ),
                $this->createField(
                    'duration_hours',
                    'Duration (hours)',
                    'number',
                    ['min' => 0.5, 'max' => 24, 'step' => 0.5],
                    false,
                    'Total duration in hours'
                ),
                $this->createField(
                    'training_location',
                    'Training Location',
                    'text',
                    [],
                    false,
                    'Where the training was conducted'
                ),
                $this->createField(
                    'training_type',
                    'Training Type',
                    'select',
                    [
                        'options' => [
                            'classroom' => 'Classroom Training',
                            'field_demo' => 'Field Demonstration',
                            'workshop' => 'Workshop',
                            'online' => 'Online Training',
                            'one_on_one' => 'One-on-One Coaching',
                            'other' => 'Other',
                        ]
                    ],
                    true,
                    'Type of training conducted'
                ),
            ],
            1
        );

        // Attendance Section
        $this->createSection(
            'attendance',
            'Attendance Records',
            'Details of participants who attended',
            [
                $this->createField(
                    'total_participants',
                    'Total Participants',
                    'number',
                    ['min' => 1, 'max' => 500],
                    true,
                    'Total number of participants'
                ),
                $this->createField(
                    'male_participants',
                    'Male Participants',
                    'number',
                    ['min' => 0, 'max' => 500],
                    false,
                    'Number of male participants'
                ),
                $this->createField(
                    'female_participants',
                    'Female Participants',
                    'number',
                    ['min' => 0, 'max' => 500],
                    false,
                    'Number of female participants'
                ),
                $this->createField(
                    'participant_categories',
                    'Participant Categories',
                    'checkbox',
                    [
                        'options' => [
                            'farmers' => 'Farmers',
                            'farm_workers' => 'Farm Workers',
                            'extension_officers' => 'Extension Officers',
                            'cooperatives' => 'Cooperative Members',
                            'youth' => 'Youth',
                            'women' => 'Women',
                            'other' => 'Other',
                        ]
                    ],
                    false,
                    'Categories of participants'
                ),
                $this->createField(
                    'attendance_list_attached',
                    'Attendance List Attached',
                    'radio',
                    [
                        'options' => [
                            'yes' => 'Yes',
                            'no' => 'No',
                        ]
                    ],
                    false,
                    'Is attendance list attached?'
                ),
            ],
            2
        );

        // Training Content Section
        $this->createSection(
            'training_content',
            'Training Content',
            'Details of what was covered in the training',
            [
                $this->createField(
                    'main_topics_covered',
                    'Main Topics Covered',
                    'textarea',
                    ['rows' => 4],
                    true,
                    'List the main topics covered in the training'
                ),
                $this->createField(
                    'training_materials_used',
                    'Training Materials Used',
                    'textarea',
                    ['rows' => 3],
                    false,
                    'List any training materials used (handouts, presentations, etc.)'
                ),
                $this->createField(
                    'practical_demonstration',
                    'Practical Demonstration',
                    'radio',
                    [
                        'options' => [
                            'yes' => 'Yes',
                            'no' => 'No',
                        ]
                    ],
                    false,
                    'Was there a practical demonstration?'
                ),
                $this->createField(
                    'demonstration_details',
                    'Demonstration Details',
                    'textarea',
                    ['rows' => 3],
                    false,
                    'Details of the practical demonstration (if applicable)'
                ),
            ],
            3
        );

        // Evaluation Section
        $this->createSection(
            'evaluation',
            'Training Evaluation',
            'Evaluation of the training session',
            [
                $this->createField(
                    'participant_feedback',
                    'Participant Feedback',
                    'textarea',
                    ['rows' => 4],
                    false,
                    'General feedback from participants'
                ),
                $this->createField(
                    'knowledge_assessment',
                    'Knowledge Assessment',
                    'select',
                    [
                        'options' => [
                            'pre_post_test' => 'Pre & Post Test',
                            'post_test_only' => 'Post Test Only',
                            'observation' => 'Observation',
                            'question_answer' => 'Q&A Session',
                            'none' => 'No Formal Assessment',
                        ]
                    ],
                    false,
                    'Method used to assess knowledge gain'
                ),
                $this->createField(
                    'overall_rating',
                    'Overall Rating',
                    'select',
                    [
                        'options' => [
                            'excellent' => 'Excellent',
                            'good' => 'Good',
                            'average' => 'Average',
                            'poor' => 'Poor',
                        ]
                    ],
                    false,
                    'Overall rating of the training session'
                ),
                $this->createField(
                    'challenges_encountered',
                    'Challenges Encountered',
                    'textarea',
                    ['rows' => 3],
                    false,
                    'Any challenges faced during the training'
                ),
                $this->createField(
                    'recommendations',
                    'Recommendations',
                    'textarea',
                    ['rows' => 3],
                    false,
                    'Recommendations for future trainings'
                ),
            ],
            4
        );

        // Follow-up Section
        $this->createSection(
            'follow_up',
            'Follow-up Actions',
            'Planned follow-up activities',
            [
                $this->createField(
                    'follow_up_required',
                    'Follow-up Required',
                    'radio',
                    [
                        'options' => [
                            'yes' => 'Yes',
                            'no' => 'No',
                        ]
                    ],
                    false,
                    'Is follow-up required?'
                ),
                $this->createField(
                    'follow_up_type',
                    'Follow-up Type',
                    'select',
                    [
                        'options' => [
                            'field_visit' => 'Field Visit',
                            'phone_call' => 'Phone Call',
                            'sms_reminder' => 'SMS Reminder',
                            'group_meeting' => 'Group Meeting',
                            'other' => 'Other',
                        ]
                    ],
                    false,
                    'Type of follow-up planned'
                ),
                $this->createField(
                    'follow_up_date',
                    'Follow-up Date',
                    'date',
                    [],
                    false,
                    'Planned follow-up date'
                ),
                $this->createField(
                    'follow_up_responsible',
                    'Follow-up Responsible',
                    'text',
                    [],
                    false,
                    'Person responsible for follow-up'
                ),
            ],
            5
        );

        return $this->sections;
    }

    public function getDefaultValues(): array
    {
        return [
            'training_date' => date('Y-m-d'),
            'training_type' => 'classroom',
            'attendance_list_attached' => 'no',
            'practical_demonstration' => 'no',
            'knowledge_assessment' => 'none',
            'follow_up_required' => 'no',
        ];
    }

    public function validate(array $data): array
    {
        $errors = parent::validate($data);

        // Custom validation for training form
        if (isset($data['male_participants']) && isset($data['female_participants']) && isset($data['total_participants'])) {
            $sum = (int)$data['male_participants'] + (int)$data['female_participants'];
            if ($sum > (int)$data['total_participants']) {
                $errors['total_participants'] = 'Sum of male and female participants cannot exceed total participants';
            }
        }

        if (isset($data['start_time']) && isset($data['end_time'])) {
            if (strtotime($data['end_time']) <= strtotime($data['start_time'])) {
                $errors['end_time'] = 'End time must be after start time';
            }
        }

        return $errors;
    }

    public function processData(array $data): array
    {
        // Calculate duration if start and end times are provided
        if (isset($data['start_time']) && isset($data['end_time'])) {
            $start = strtotime($data['start_time']);
            $end = strtotime($data['end_time']);
            if ($end > $start) {
                $duration = ($end - $start) / 3600; // Convert to hours
                $data['duration_hours'] = round($duration, 1);
            }
        }

        // Ensure participant counts are integers
        if (isset($data['total_participants'])) {
            $data['total_participants'] = (int)$data['total_participants'];
        }
        if (isset($data['male_participants'])) {
            $data['male_participants'] = (int)$data['male_participants'];
        }
        if (isset($data['female_participants'])) {
            $data['female_participants'] = (int)$data['female_participants'];
        }

        return $data;
    }
}
