<?php
declare(strict_types=1);

namespace Models;

class City extends Model
{
    protected string $table = 'cities';
    protected array $fillable = ['name'];
}
