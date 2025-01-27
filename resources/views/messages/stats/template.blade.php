<b>@lang('stats.title')</b> > <b>{{ $title }}</b><br>
<br>
@lang('stats.range.today', ['value' => number_format($today, thousands_separator: '˙')])<br>
@lang('stats.range.yesterday', ['value' => number_format($yesterday, thousands_separator: '˙')])<br>
@lang('stats.range.last_7_days', ['value' => number_format($last_7_days, thousands_separator: '˙')])<br>
@lang('stats.range.last_30_days', ['value' => number_format($last_30_days, thousands_separator: '˙')])<br>
@lang('stats.range.year', ['value' => number_format($year, thousands_separator: '˙')])<br>
@isset($total)
@lang('stats.range.total', ['value' => number_format($total, thousands_separator: '˙')])<br>
@endisset
<br>
@lang('stats.last_update')<br>
{{ $last_update }}
