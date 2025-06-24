<x-print title="Examination Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Examinations' => route('examinations.index', ['academic_subject' => $examination->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
        ]"/>
    </x-slot>

    @include('examinations.details', [
        'examination' => $examination,
        'sections' => $sections,
        'heading' => $heading,
        'shouldCreate' => false,
    ])

    <style>
        @media print {
            html, body {
                height: auto !important;
                overflow: visible !important;
            }
            .print\:break {
                page-break-before: always;
            }
            * {
                overflow: visible !important;
            }
            /* Remove scrollbars and force content to expand */
            .overflow-auto, .overflow-scroll, .overflow-y-auto, .overflow-x-auto {
                overflow: visible !important;
            }
        }
        @media print {
            html, body, .container, .w-full, .h-full, .min-h-screen, .print-area, .print-wrapper {
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
            }
            * {
                overflow: visible !important;
                box-shadow: none !important;
            }
            .page-break, .print\:break {
                page-break-before: always;
            }
        }

    </style>
</x-print>
