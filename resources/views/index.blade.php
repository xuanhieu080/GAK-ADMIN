<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{$config['name']}}</title>
    <meta name="bingbot" content="follow, noindex">
    <meta name="googlebot" content="follow, noindex">
    <meta name="robots" content="follow, noindex">
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.css" />

    <!-- If you are using premium features: -->
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5-premium-features/42.0.0/ckeditor5-premium-features.css" />


    <script>
        window.AppConfig = {
            name: '{{ env('APP_NAME') }}',
            logo: '{{ env('APP_LOGO') }}',
            url: '{{ env('APP_URL') }}',
            csrf: '{{ csrf_token() }}',
            defaultLocale: '{{ env('APP_LOCALE', 'en') }}',
            defaultTimezone: '{{ env('APP_TIMEZONE', 'UTC') }}',
            locales: {
                en: {!! json_encode(\Illuminate\Support\Facades\Lang::get('frontend', [], 'en')) !!},
                mk: {!! json_encode(\Illuminate\Support\Facades\Lang::get('frontend', [], 'mk')) !!},
                vi: {!! json_encode(\Illuminate\Support\Facades\Lang::get('frontend', [], 'vi')) !!},
            }
        }
        window.WebConfig = {!! \App\Models\Config::pluck('description', 'code') !!}
    </script>
</head>

<body>
<noscript>
    <strong>We're sorry but this application doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
</noscript>

<div id="app"></div>
@vite(['resources/scss/main.scss', 'resources/js/main.js'])
</body>

</html>
