<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'display_name',
        'daily_cigarettes',
        'pack_cost',
        'quit_date',
        'earned_badges',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'quit_date' => 'date',
            'earned_badges' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
