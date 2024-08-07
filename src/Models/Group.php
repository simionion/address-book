<?php
declare(strict_types=1);

namespace Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    protected $table = 'groups';
    protected $fillable = ['name'];
    public $timestamps = false;

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'group_contacts', 'group_id', 'contact_id');
    }

    public function parentGroups(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'group_inheritance', 'child_group_id', 'parent_group_id');
    }

    public function childGroups(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'group_inheritance', 'parent_group_id', 'child_group_id');
    }

    public function getAllParentGroups(): array
    {
        $parents = $this->parentGroups;

        foreach ($this->parentGroups as $parent) {
            $parents = $parents->merge($parent->getAllParentGroups());
        }

        return $parents->unique('id')->all();
    }
}
