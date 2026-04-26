@component('mail::message')
# Payment Failed

Dear {{ $customerName }},

Unfortunately, we were unable to process payment for your order **{{ $orderNumber }}**.

## Reason

{{ $failureMessage }}

## What You Can Do

You can try completing your order by:

1. Going back to your cart and attempting checkout again
2. Using a different payment method
3. Contacting your bank if you believe there's an issue with your card

Your cart items are still saved, so you won't lose anything if you need to try again.

## Need Help?

If you continue to experience issues, please contact our support team:

- **Email:** support@example.com
- **Phone:** 1-800-XXX-XXXX

We're here to help!

Best regards,  
The {{ config('app.name') }} Team

@component('mail::subcopy')
This email was sent to you because a payment attempt failed on {{ config('app.name') }}.
@endcomponent
@endcomponent
