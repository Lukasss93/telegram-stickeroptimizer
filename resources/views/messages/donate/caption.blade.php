{{'@'.config('bot.username')}}<br>
<br>
@if(config('donation.message_in_caption'))
<a href="https://t.me/{{config('bot.username')}}?start=donate">@lang('donate.consider_donation')</a>
@endif
