<?php
declare(strict_types=1);

namespace Models;

class City extends Model
{
    protected string $table = 'cities';
    protected array $fillable = ['name'];

    public function contacts(int $cityId): array
    {
        return $this->hasMany(Contact::class, 'city_id', $cityId);
    }
}
