@props(['currentLocale' => null])
@php
    $currentLocale ??= request()->getLocale();
    $otherLocales = collect(inspirecms()->getAllAvailableLanguages())
        ->where(fn ($dto, $l) => $l != $currentLocale)
        ->mapWithKeys(fn ($item) => [$item->code => $item->getLabel($currentLocale)])
        ->all();
@endphp
    
<div class="dropdown">
    <button class="btn btn-white dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        {{ strtoupper($currentLocale) }}
    </button>
    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
        @foreach ($otherLocales as $locale => $langDto)
            <li><a class="dropdown-item" href="{{ url("/$locale") }}">{{ strtoupper($locale) }}</a></li>
        @endforeach
    </ul>
</div>