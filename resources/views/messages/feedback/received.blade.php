@lang('feedback.new')<br>
@lang('common.from') {{ $from }}
@isset($username)
    ({{ '@'.$username }})
@endisset
[{{ $user_id }}]<br>
@lang('common.message')<br>
{!! $message  !!}
