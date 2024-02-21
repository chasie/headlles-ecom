<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasMacros;

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
