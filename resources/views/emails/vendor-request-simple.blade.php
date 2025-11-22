@php
    $appName = config('app.name');
    $primaryColor = config('branding.colors.primary');
    $secondaryColor = config('branding.colors.secondary');
    $textColor = config('branding.colors.text');
    $lightGray = config('branding.colors.light_gray');
    $borderColor = config('branding.colors.border');
@endphp

<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Request</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: {{ $textColor }}; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%); padding: 40px 20px; text-align: center; color: white; }
        .header h1 { font-size: 28px; margin-bottom: 10px; font-weight: 700; }
        .header p { font-size: 16px; opacity: 0.95; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 22px; font-weight: 600; color: {{ $primaryColor }}; margin-bottom: 20px; }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 18px; font-weight: 600; color: {{ $primaryColor }}; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid {{ $borderColor }}; }
        .section p { margin-bottom: 12px; line-height: 1.8; font-size: 15px; }
        .details-box { background-color: {{ $lightGray }}; padding: 20px; border-radius: 6px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; padding-bottom: 10px; border-bottom: 1px solid {{ $borderColor }}; }
        .detail-row:last-child { border-bottom: none; margin-bottom: 0; }
        .detail-label { font-weight: 600; color: {{ $primaryColor }}; }
        .detail-value { color: {{ $textColor }}; }
        .activities-list { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .activity-badge { display: inline-block; background-color: {{ $primaryColor }}; color: white; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 500; }
        .divider { height: 1px; background-color: {{ $borderColor }}; margin: 20px 0; }
        .footer { background-color: {{ $lightGray }}; padding: 30px; text-align: center; border-top: 1px solid {{ $borderColor }}; font-size: 13px; color: #666666; }
        .footer p { margin-bottom: 10px; }
        .footer a { color: {{ $primaryColor }}; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vendor Request Received</h1>
            <p>Thank you for your interest in becoming a vendor</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $vendorRequest->company_name }},
            </div>

            <div class="section">
                <p>Thank you for your interest in becoming a vendor on {{ $appName }}! We have received your vendor request and will review it shortly.</p>
            </div>

            <div class="section">
                <div class="section-title">Request Details</div>
                <div class="details-box">
                    <div class="detail-row">
                        <span class="detail-label">Company Name:</span>
                        <span class="detail-value">{{ $vendorRequest->company_name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">{{ $vendorRequest->email }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">{{ $vendorRequest->phone }}</span>
                    </div>
                    @if($vendorRequest->activities && $vendorRequest->activities->count() > 0)
                    <div class="detail-row">
                        <span class="detail-label">Activities:</span>
                        <span class="detail-value">
                            <div class="activities-list">
                                @foreach($vendorRequest->activities as $activity)
                                    <span class="activity-badge">{{ $activity->getTranslation('name', app()->getLocale()) }}</span>
                                @endforeach
                            </div>
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="section">
                <div class="section-title">What Happens Next?</div>
                <p>Our team will carefully review your vendor request and verify all the information you provided.</p>
                <p>We will contact you within 2-3 business days with a decision. If we need any additional information, we will reach out to you directly.</p>
            </div>

            <div class="divider"></div>

            <div class="section">
                <div class="section-title">Need Help?</div>
                <p>If you have any questions about your vendor request or need assistance, please don't hesitate to contact us.</p>
                <p>Support Email: <a href="mailto:{{ config('branding.email.support_email') }}">{{ config('branding.email.support_email') }}</a></p>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing {{ $appName }} as your platform to grow your business.</p>
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
