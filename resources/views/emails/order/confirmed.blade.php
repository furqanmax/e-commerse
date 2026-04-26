@component('mail::message')
# Order Confirmed! 🎉

Dear {{ $customerName }},

Great news! Your order **{{ $orderNumber }}** has been confirmed and is being processed.

## Order Details

**Order Number:** {{ $orderNumber }}  
**Total Amount:** ${{ $orderTotal }}  
**Estimated Delivery:** {{ $estimatedDelivery }}

### Items Ordered

@foreach($items as $item)
- {{ $item->name }} × {{ $item->quantity }}
@endforeach

## What's Next?

1. **Order Processing:** We're preparing your items for shipment
2. **Shipping Notification:** You'll receive an email when your order ships
3. **Delivery:** Your package will arrive within the estimated timeframe

## Need Help?

If you have any questions about your order, please don't hesitate to contact our support team:

- **Email:** support@example.com
- **FAQ:** https://example.com/faq

Thank you for shopping with us!

Best regards,  
The {{ config('app.name') }} Team

@component('mail::subcopy')
This email was sent to you because you placed an order on {{ config('app.name') }}. If you have any questions, please contact our support team.
@endcomponent
@endcomponent
