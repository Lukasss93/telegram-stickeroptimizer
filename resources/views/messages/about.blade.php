<b>@lang('about.bot')</b><br>
@lang('about.name',['value'=>config('bot.name')])<br>
@lang('about.username',['value'=>config('bot.username')])<br>
@lang('about.version',['value'=>config('bot.version')])<br>

@if(config('bot.source'))
    @lang('about.source',['value'=>'<a href="'.config('bot.source').'">Github</a>'])<br>
@endif

@if(config('bot.changelog'))
    @lang('about.changelog',['value'=>'<a href="'.config('bot.changelog').'">Telegraph</a>'])<br>
@endif
<br>

<b>@lang('about.developer')</b><br>
@lang('about.name',['value'=>config('developer.name')])<br>
@lang('about.username',['value'=>config('developer.username')])<br>
@lang('about.email',['value'=>config('developer.email')])<br>
@lang('about.website',['value'=>config('developer.website')])<br>
