@lang('settings.title')<br>
@lang('settings.description')<br>
<br>
@lang('settings.news'): {{ $news ? __('settings.enabled') : __('settings.disabled') }}<br>
@lang('settings.language.title'): {{ $language }}<br>
@lang('settings.watermark.title'): {{ $watermark ? __('settings.enabled') : __('settings.disabled') }}<br>
@lang('settings.template.title', ['type' => $template])<br>
@lang('settings.trim.enable'): {{ $trim ? __('settings.enabled') : __('settings.disabled') }}<br>
