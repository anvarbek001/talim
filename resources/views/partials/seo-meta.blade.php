@php
    $seoTitle ??= config('seo.default_title');
    $seoDescription ??= config('seo.default_description');
    $seoKeywords ??= config('seo.default_keywords');
    $seoImage ??= config('seo.og_image');
    $seoType ??= 'website';
    $seoRobots ??= 'index, follow';
    $seoUrl ??= url()->current();
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
@if ($seoKeywords)
    <meta name="keywords" content="{{ $seoKeywords }}">
@endif
<meta name="robots" content="{{ $seoRobots }}">
<meta name="author" content="{{ config('seo.site_name') }}">
<link rel="canonical" href="{{ $seoUrl }}">
<link rel="icon" href="{{ asset('favicon.ico') }}">
<meta name="theme-color" content="#141B33">

{{-- Google Search Console va Yandex Webmaster tasdiqlash kodlari (.env orqali kiritiladi) --}}
@if (config('seo.google_verification'))
    <meta name="google-site-verification" content="{{ config('seo.google_verification') }}">
@endif
@if (config('seo.yandex_verification'))
    <meta name="yandex-verification" content="{{ config('seo.yandex_verification') }}">
@endif

{{-- Open Graph (Facebook, Telegram, LinkedIn va h.k.) --}}
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:site_name" content="{{ config('seo.site_name') }}">
<meta property="og:locale" content="uz_UZ">
@if ($seoImage)
    <meta property="og:image" content="{{ asset(ltrim($seoImage, '/')) }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
@if ($seoImage)
    <meta name="twitter:image" content="{{ asset(ltrim($seoImage, '/')) }}">
@endif
