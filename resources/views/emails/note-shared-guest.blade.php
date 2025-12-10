<x-emails.layouts.default
    :title="'Note Shared: ' . $note->title"
    :previewText="$sharer->name . ' shared a note with you: ' . $note->title"
    :showLogo="true"
    :headerTitle="'📝 Note Shared with You!'"
    :headerSubtitle="$sharer->name . ' shared a note'"
>
    <x-emails.greeting>
        Hello,
    </x-emails.greeting>

    <x-emails.paragraph>
        <strong>{{ $sharer->name }}</strong> from {{ config('app.name') }} has shared a note with you.
        You have {{ $canEdit ? 'edit' : 'view' }} access to this note.
    </x-emails.paragraph>

    <!-- Note Info Box -->
    <x-emails.info-box type="blue">
        <strong style="font-size: 18px; display: block; margin-bottom: 12px;">{{ $note->title }}</strong>

        @if($note->academicSubject)
            <span style="display: block; margin-bottom: 4px;">📚 Subject: {{ $note->academicSubject->name }}</span>
        @endif

        @if($note->book)
            <span style="display: block; margin-bottom: 4px;">📖 Book: {{ $note->book->title }}</span>
        @endif

        <span style="display: block; margin-top: 12px;">
            <strong>Permission:</strong> {{ $canEdit ? '✏️ Can Edit' : '👁️ View Only' }}
        </span>
    </x-emails.info-box>

    @if($note->content)
        <x-emails.paragraph>
            <strong>Preview:</strong><br>
            {{ Str::limit(strip_tags($note->content), 200) }}
        </x-emails.paragraph>
    @endif

    <x-emails.button :url="$noteUrl">
        📝 View Note Now
    </x-emails.button>

    <x-emails.info-box type="yellow">
        <strong>📧 Note:</strong><br>
        • This email was sent to: {{ $guestEmail }}<br>
        • You may need to create an account to access this note<br>
        • Your access will be ready once you sign up
    </x-emails.info-box>

    <x-emails.paragraph>
        Join {{ config('app.name') }} to collaborate and share knowledge!
    </x-emails.paragraph>

    <p style="color: #6b7280; font-size: 14px; font-style: italic; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
        Happy learning!<br>
        <strong>The {{ config('app.name') }} Team</strong>
    </p>
</x-emails.layouts.default>
