<?php
declare(strict_types=1);

namespace Models;

use PDO;

class Tag extends Model
{
    protected string $table = 'tags';
    protected array $fillable = ['name'];
}
