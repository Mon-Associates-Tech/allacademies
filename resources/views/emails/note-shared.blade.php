<x-emails.layouts.default
    :title="'Note Shared: ' . $note->title"
    :previewText="$sharer->name . ' shared a note with you: ' . $note->title"
    :showLogo="true"
    :headerTitle="'📝 Note Shared with You!'"
    :headerSubtitle="$sharer->name . ' shared a note'"
>
    <x-emails.greeting>
        Hello {{ $recipient->name }},
    </x-emails.greeting>

    <x-emails.paragraph>
        <strong>{{ $sharer->name }}</strong> has shared a note with you on {{ config('app.name') }}.
        You now have {{ $canEdit ? 'edit' : 'view' }} access to this note.
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

    @if($canEdit)
        <x-emails.info-box type="green">
            <strong>💡 What you can do:</strong><br>
            • Read and study the note content<br>
            • Edit and improve the note<br>
            • Add your own insights and observations<br>
            • Collaborate with {{ $sharer->name }}
        </x-emails.info-box>
    @endif

    <x-emails.paragraph>
        Make the most of this shared knowledge!
    </x-emails.paragraph>

    <p style="color: #6b7280; font-size: 14px; font-style: italic; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
        Best regards,<br>
        <strong>The {{ config('app.name') }} Team</strong>
    </p>
</x-emails.layouts.default>
