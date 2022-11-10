<?php

namespace App\Models;

use App\Rules\HexColorRule;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Lukasss93\ModelSettings\Traits\HasSettingsTable;
use SergiX44\Nutgram\Telegram\Types\User\User;

/**
 * App\Models\Chat
 *
 * @property int $chat_id
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string|null $language_code
 * @property Carbon|null $started_at
 * @property Carbon|null $blocked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Chat newModelQuery()
 * @method static Builder|Chat newQuery()
 * @method static Builder|Chat query()
 * @method static Builder|Chat whereBlocked($value)
 * @method static Builder|Chat whereChatId($value)
 * @method static Builder|Chat whereCreatedAt($value)
 * @method static Builder|Chat whereFirstName($value)
 * @method static Builder|Chat whereLanguageCode($value)
 * @method static Builder|Chat whereLastName($value)
 * @method static Builder|Chat whereStatus($value)
 * @method static Builder|Chat whereUpdatedAt($value)
 * @method static Builder|Chat whereUsername($value)
 * @method static Builder|Chat whereSettings($setting, $operator, $value = null, $filterOnMissing = null)
 * @mixin Eloquent
 */
class Chat extends Model
{
    use HasFactory;
    use HasSettingsTable;

    protected $primaryKey = 'chat_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected static $unguarded = true;
    protected $dates = ['started_at', 'blocked_at'];

    protected bool $initSettings = true;

    public function defaultSettings(): array
    {
        return [
            'news' => true,
            'language' => 'en',
            'watermark' => [
                'opacity' => 0,
                'position' => 'middle-center',
                'text' => [
                    'content' => null,
                    'size' => 14,
                    'color' => '#ffffff',
                ],
                'border' => [
                    'size' => 0,
                    'color' => '#000000',
                ],
            ],
            'template' => 'sticker',
        ];
    }

    public function settingsRules(): array
    {
        return [
            'watermark.opacity' => 'integer|min:0|max:100',
            'watermark.text.content' => 'nullable|max:100',
            'watermark.text.size' => 'integer|min:1|max:100',
            'watermark.text.color' => [new HexColorRule],
            'watermark.border.size' => 'integer|min:0|max:10',
            'watermark.border.color' => [new HexColorRule],
        ];
    }

    public static function findFromUser(?User $user): ?Chat
    {
        if ($user === null) {
            return null;
        }

        $chat = self::find($user->id);

        return $chat ?? null;
    }

    public function scopeWhereSettings(
        Builder $query,
        string $setting,
        string $operator,
        $value,
        bool $filterOnMissing = null
    ): Builder {
        return $query->where(function (Builder $query) use ($value, $operator, $setting, $filterOnMissing) {
            return $query->when(
                $filterOnMissing,
                function (Builder $query) use ($value, $operator, $setting) {
                    return $query
                        ->whereDoesntHave('modelSettings')
                        ->orWhereHas(
                            'modelSettings',
                            fn (Builder $query) => $query->where("settings->$setting", $operator, $value)
                        );
                },
                function (Builder $query) use ($value, $operator, $setting) {
                    $query->whereHas(
                        'modelSettings',
                        fn (Builder $query) => $query->where("settings->$setting", $operator, $value)
                    );
                }

            );
        });
    }
}
