<?php

use SergiX44\Nutgram\Nutgram;

Route::get('/hook', fn () => app(Nutgram::class)->run());
