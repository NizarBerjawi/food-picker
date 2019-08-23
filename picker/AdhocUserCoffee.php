<?php

namespace Picker;

use Picker\CoffeeRun;
use Picker\UserCoffee\Scopes\AdhocScope;
use Illuminate\Database\Eloquent\Builder;

class AdhocUserCoffee extends UserCoffee
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sugar', 'coffee_id',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AdhocScope);

        static::creating(function($model) {
            $model->forceFill([
                'start_time' => now()->format('G:i'),
                'end_time'   => now()->format('G:i'),
                'days'       => [strtolower(now()->shortEnglishDayOfWeek)],
                'is_adhoc'   => true,
            ]);
        });
    }
}
