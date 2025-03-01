<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';
    protected $fillable = ['type','user_id', 'plan_id','status','start_date','end_date'];
    
    // Check and update expired subscriptions
    public static function updateExpiredSubscriptions($userId, $type)
    {
        self::where('user_id', $userId)
            ->where('type', $type)
            ->where('status', 'active')
            ->whereDate('end_date', '<', Carbon::now())
            ->update(['status' => 'expired']);
    }
    // Check if a user has an active security subscription
    public static function hasActiveSecurity($userId, $type)
    {
        // First, update any expired subscriptions
        self::updateExpiredSubscriptions($userId, $type);

        // Then, check for an active security subscription
        return self::where('user_id', $userId)
            ->where('type', $type) // type 2 = security plan,1 = Subscription
            ->where('status', 'active')
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->exists();
    }
}
