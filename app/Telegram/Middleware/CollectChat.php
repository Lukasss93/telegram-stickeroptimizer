<?php

namespace App\Telegram\Middleware;

use App\Models\Chat;
use Illuminate\Support\Facades\DB;
use Sentry\State\Scope;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ChatType;
use function Sentry\configureScope;

class CollectChat
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $user = $bot->user();

        if ($user === null) {
            return;
        }

        $chat = DB::transaction(function () use ($user, $bot) {

            //save or update chat
            $chat = Chat::updateOrCreate([
                'chat_id' => $user->id,
            ], [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'language_code' => $user->language_code,
                'blocked_at' => null,
            ]);

            if ($chat->started_at === null && $bot->message()?->chat?->type === ChatType::PRIVATE) {
                $chat->started_at = now();
                $chat->save();
            }

            return $chat;
        });

        $bot->set(Chat::class, $chat);

        configureScope(function(Scope $scope) use ($chat) {
            $scope->setUser($chat->toArray());
        });

        $next($bot);
    }
}
