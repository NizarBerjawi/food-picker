<?php

namespace Picker\UserCoffee\Filters;

use Picker\Coffee;
use Picker\Support\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class UserCoffeeFilters extends Filter
{
    /**
     * Filter by archived entries.
     *
     * @return Builder
     */
    protected function coffeeType()
    {
        $coffee = Coffee::whereSlug($this->request->get('coffee_type'));

        if (!$coffee->exists()) { return $this->builder; }

        return $this->builder->byType($coffee->first());
    }
}