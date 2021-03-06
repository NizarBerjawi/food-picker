<?php

namespace App\Models;

use Carbon\Carbon;
use App\Support\Filters\Filterable;
use App\Support\Traits\{ExcludesFromQuery, HasDays};
use Illuminate\Database\Eloquent\{Builder, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\URL;

class UserCoffee extends Pivot
{
    use ExcludesFromQuery, Filterable, HasDays, SoftDeletes;

    /**
      * The table associated with the model.
      *
      * @var string
      */
    protected $table = 'user_coffee';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sugar' => 'integer',
        'days' => 'array',
        'is_adhoc' => 'boolean',
    ];

    /**
     * The column that contains the days
     *
     * @var string
     */
    protected $daysColumn = 'days';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sugar', 'start_time', 'end_time', 'days', 'user_id', 'coffee_id',
    ];

    /**
     * Get the coffee this user's selection belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coffee()
    {
      return $this->belongsTo(Coffee::class)->withTrashed();
    }

    /**
     * Get the user that this coffee belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the coffee runs that this user coffee is part off.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function runs()
    {
        return $this->belongsToMany(CoffeeRun::class,
            'coffee_run_user_coffee', 'user_coffee_id', 'coffee_run_id'
        );
    }

    /**
     * Get all user coffees that are scheduled before a
     * certain time.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $time  "G:i"  or "h:i A" time format
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBefore(Builder $query, string $time)
    {
        $time = date("G:i", strtotime($time));

        return $query->whereRaw("time(start_time) <= time('$time')");
    }

    /**
     * Get all user coffees that are scheduled after a
     * certain time.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $time  "G:i"  or "h:i A" time format
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAfter(Builder $query, string $time)
    {
        $time = date("G:i", strtotime($time));

        return $query->whereRaw("time(end_time) >= time('$time')");
    }

    /**
     * Get all user coffees that fall within a certain time slot
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $time  "G:i"  or "h:i A" time format
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetween(Builder $query, string $start, string $end)
    {
      return $query->after($start)->before($end);
    }

    /**
     * Get all the user coffees that are on today's order
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday(Builder $query)
    {
        $today = Carbon::today();

        return $query->between(
            $today->startOfDay()->format('G:i'),
            $today->endOfDay()->format('G:i')
        )->days([strtolower($today->shortEnglishDayOfWeek)]);
    }

    /**
     * Get all the user coffees that could be part of the next
     * coffee run.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNextRun(Builder $query)
    {
        $now = Carbon::now();

        // The user is not allowed to create coffees that overlap
        // in time range. We can safely assume that there will only be
        // a maximum of 1 coffee per user at any given time/day
        // combination
        return $query->between($now->format('G:i'), $now->format('G:i'))
            ->days([strtolower($now->shortEnglishDayOfWeek)]);
    }

    /**
     * Get user coffees by their type
     *
     * @param \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType(Builder $query, Coffee $coffee)
    {
        return $query->whereHas('coffee', function(Builder $query) use ($coffee) {
            $query->withTrashed()->where('slug', $coffee->slug);
        });
    }

    /**
     * Get user coffees that belong to a specified coffee run
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\CoffeeRun  $run
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByRun(Builder $query, CoffeeRun $run)
    {
        return $query->whereHas('runs', function($query) use ($run) {
            $query->where('id', $run->id);
        });
    }

    /**
     * Get user coffees without adhoc ones
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutAdhoc(Builder $query)
    {
        return $query->where('is_adhoc', false);
    }

    /**
     * Get a signed url that allows the user to replace this
     * coffee with another adhoc coffee for a particular coffee run.
     *
     * @param  \App\Models\CoffeeRun  $run
     * @return string
     */
    public function getAdhocUrl(CoffeeRun $run = null)
    {
        if (is_null($run)) {
            $run = $this->runs()->lastRun()->first();
        }

        $expires = now()->addHours(1);

        return URL::temporarySignedRoute('dashboard.adhoc.create', $expires, [
            'run' => $run->id,
            'coffee_id' => $this->id,
        ]);
    }

    /**
     * Check if this coffee is of a certain coffee type.
     *
     * @param  \App\Models\Coffee  $coffee
     * @return bool
     */
    public function isOfType(Coffee $coffee)
    {
        return $this->coffee->is($coffee);
    }

    /**
     * Check if this coffee is not of a certain coffee type.
     *
     * @param  \App\Models\Coffee  $coffee
     * @return bool
     */
     public function isNotOfType(Coffee $coffee)
     {
         return !$this->isOfType($coffee);
     }

     /**
      * Get the type of the user's coffee
      *
      * @return string
      */
     public function getTypeAttribute()
     {
         return $this->coffee->name;
     }

     /**
      * Set the start time of this coffee
      *
      * @return void
      */
     public function setStartTimeAttribute($value)
     {
         $this->attributes['start_time'] = date("G:i", strtotime($value));
     }

     /**
      * Set the end time of this coffee
      *
      * @return void
      */
     public function setEndTimeAttribute($value)
     {
         $this->attributes['end_time'] = date("G:i", strtotime($value));
     }

     /**
      * Get the start time of this coffee
      *
      * @return string
      */
     public function getStartTimeAttribute($value)
     {
         return date("h:i A", strtotime($value));
     }

     /**
      * Get the end time of this coffee
      *
      * @return string
      */
     public function getEndTimeAttribute($value)
     {
         return date("h:i A", strtotime($value));
     }

    /**
     * Get the string representation of the user's coffee.
     *
     * @return string
     */
    public function __toString()
    {
        return "{$this->getType()} between
            $this->start_time and $this->end_time
            every {$this->getFormattedDays()}";
    }
}
