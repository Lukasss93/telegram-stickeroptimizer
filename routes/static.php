<?php

use SergiX44\Nutgram\Nutgram;

Route::post('/hook', fn () => app(Nutgram::class)->run());
