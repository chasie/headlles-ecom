<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;

class UserPermission extends BaseModel
{
    use HasMacros;

    protected $fillable = ['handle'];

    /**
     * Return the user relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
