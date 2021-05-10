@lang('donate.consider_donation')<br>
@lang('donate.appreciated')<br>
@if(!empty(config('telegram.bot.donations.terms')))
    <br>
    <a href="{{ config('telegram.bot.donations.terms') }}">@lang('donate.terms')</a>
@endif

