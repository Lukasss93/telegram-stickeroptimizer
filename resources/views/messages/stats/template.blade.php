<b>@lang('stats.title')</b> > <b>{{ $title }}</b><br>
<br>
@lang('stats.range.yesterday', ['value' => $yesterday])<br>
@lang('stats.range.today', ['value' => $today])<br>
@lang('stats.range.week', ['value' => $week])<br>
@lang('stats.range.month', ['value' => $month])<br>
@lang('stats.range.year', ['value' => $year])<br>
@isset($total)
@lang('stats.range.total', ['value' => $total])<br>
@endisset
<br>
@lang('stats.last_update')<br>
{{ $lastUpdate }}
