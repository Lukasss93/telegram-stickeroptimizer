@lang('donate.consider_donation')<br>
@lang('donate.appreciated')<br>
@if(!empty(config('donation.terms')))
<br>
<a href="{{ config('donation.terms') }}">@lang('donate.terms')</a>
@endif

