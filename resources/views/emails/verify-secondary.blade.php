<x-mail::message>
# Verify your email address — {{ config('app.name') }}

You requested to add this email to your account. Use the verification code below to confirm it.

<x-mail::panel>
# {{ $code }}
</x-mail::panel>

This code is valid for **15 minutes**. Do not share it with anyone.

If you did not request this, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
