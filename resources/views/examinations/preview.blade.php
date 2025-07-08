<x-print title="Examination Preview">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Examinations' => route('examinations.index', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
        ]"/>
    </x-slot>

    <x-slot:action>
        <div class="text-right">
            <x-link.secondary
                :to="route('examinations.create', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"> Go back
            </x-link.secondary>
        </div>
    </x-slot:action>

    @include('examinations.details', [
         'sections' => $sections,
         'heading' => $heading,
         'examination' => $title,
         'isPreview' => true,
         'shouldCreate' => true,
         'team_id' => $previewData['team_id'],
         'creator_id' => $previewData['creator_id'],
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
