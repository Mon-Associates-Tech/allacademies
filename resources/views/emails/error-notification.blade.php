@component('mail::message')
# 🚨 Error Notification

An error occurred in **{{ $emailData['app_name'] }}** ({{ $emailData['environment'] }})

@component('mail::panel')
## Error Details
**Message:** {{ $emailData['error_message'] }}  
**Timestamp:** {{ $emailData['timestamp'] }}
@endcomponent

@isset($emailData['additional_data'])
## Request Information
@component('mail::table')
| Field | Value |
|:------|:------|
@foreach($emailData['additional_data'] as $key => $value)
@if($key !== 'trace')
| **{{ ucwords(str_replace('_', ' ', $key)) }}** | {{ is_array($value) ? json_encode($value) : $value }} |
@endif
@endforeach
@endcomponent
@endisset

@isset($emailData['trace'])
## Stack Trace (Top 5)
```
@foreach($emailData['trace'] as $index => $frame)
{{ $index + 1 }}. {{ $frame['class'] ?? '' }}{{ $frame['class'] ? '::' : '' }}{{ $frame['function'] }}()
   at {{ $frame['file'] }}:{{ $frame['line'] }}
@endforeach
```
@endisset

@component('mail::button', ['url' => config('app.url')])
View Application
@endcomponent

Please investigate this issue as soon as possible.

Thanks,  
{{ config('app.name') }} Team
@endcomponent
