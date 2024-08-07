<?php
declare(strict_types=1);

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $table = 'cities';
    protected $fillable = ['name'];
    public $timestamps = false;

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'city_id');
    }
}
