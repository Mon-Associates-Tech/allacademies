@component('mail::message')
    # Error Notification

    An error occurred in {{ $emailData['app_name'] }} ({{ $emailData['environment'] }})

    ## Error Details
    **Message:** {{ $emailData['error_message'] }}
    **Timestamp:** {{ $emailData['timestamp'] }}

    @isset($emailData['additional_data'])
        ## Additional Information
        @foreach($emailData['additional_data'] as $key => $value)
            - **{{ $key }}**: {{ is_array($value) ? json_encode($value) : $value }}
        @endforeach
    @endisset

    Please investigate this issue as soon as possible.

    Thanks,
    {{ config('app.name') }} Team
@endcomponent
