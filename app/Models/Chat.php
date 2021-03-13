<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use SergiX44\Nutgram\Telegram\Types\User;

/**
 * App\Models\Chat
 *
 * @property int $chat_id
 * @property string $type
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string|null $language_code
 * @property bool $started
 * @property bool $blocked
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
 * @method static Builder|Chat whereType($value)
 * @method static Builder|Chat whereUpdatedAt($value)
 * @method static Builder|Chat whereUsername($value)
 * @mixin Eloquent
 */
class Chat extends Model
{
    use HasFactory;

    protected $primaryKey = 'chat_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'started' => 'boolean',
        'blocked' => 'boolean',
    ];

    public static function findFromUser(?User $user): ?Chat
    {
        if ($user === null) {
            return null;
        }

        $chat = self::find($user->id);

        return $chat ?? null;
    }
}
