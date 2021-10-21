@lang('settings.title') > @lang('settings.language.title')<br>
@lang('settings.language.description')<br>
<br>
@lang('settings.language.title'): {{ $language }}<br>
<br>
@if(isset($localization) && $localization!=='')
@lang('settings.language.more') <a href="{{ $localization }}">@lang('settings.language.click')</a>
@endif
