<x-layouts.app pageName="Student Help">
    <x-slot name="title">Help &amp; Support</x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Using your student portal</h2>
            <ul class="mt-4 list-inside list-disc space-y-2 text-sm text-gray-600 dark:text-gray-300">
                <li>Open Courses or Lessons to review your learning materials.</li>
                <li>Use Assessments and Assignments to access your learning activities.</li>
                <li>Check your schedule, report cards, and fee records from the student dashboard.</li>
                <li>For school-specific questions, send a message to your school staff.</li>
            </ul>
            <a href="{{ route('students.messages.compose') }}" class="mt-5 inline-flex rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">Contact school staff</a>
        </div>
    </div>
</x-layouts.app>