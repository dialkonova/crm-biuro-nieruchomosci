<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientCommentHistory extends Model
{
    public $timestamps = false;

    protected $table = 'client_comment_history';

    protected $fillable = [
        'client_id',
        'user_id',
        'old_comment',
        'new_comment',
        'ip_address',
        'created_at',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}