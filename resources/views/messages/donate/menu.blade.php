@lang('donate.consider_donation')<br>
@lang('donate.appreciated')<br>
@if(!empty(config('bot.donations.terms')))
<br>
<a href="{{ config('bot.donations.terms') }}">@lang('donate.terms')</a>
@endif

