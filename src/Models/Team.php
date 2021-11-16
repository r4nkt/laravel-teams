<?php

namespace R4nkt\Teams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Events\CreatingTeam;
use R4nkt\Teams\Events\DeletingTeam;
use R4nkt\Teams\Events\TeamCreated;
use R4nkt\Teams\Events\TeamDeleted;
use R4nkt\Teams\Events\TeamUpdated;
use R4nkt\Teams\Teams;

class Team extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'custom_data',
    ];

    /** @var array */
    protected $casts = [
        'owner_id' => 'int',
        'custom_data' => 'array',
    ];

    /** @var array */
    protected $dispatchesEvents = [
        'creating' => CreatingTeam::class,
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleting' => DeletingTeam::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the owner of the team.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Teams::memberModel());
    }

    /**
     * Get all of the team's members including its owner.
     */
    public function allMembers(): Collection
    {
        return $this->members->merge([$this->owner]);
    }

    /**
     * Get all of the members that belong to the team.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Teams::memberModel(), 'member_team', 'team_id', 'member_id')
            ->using(Membership::class)
            ->as('membership')
            ->withPivot('attributes')
            ->withTimestamps();
    }

    /**
     * Determine if the given member belongs to the team.
     */
    public function hasMember(BelongsToTeam $member): bool
    {
        return $this->members->contains($member) || $member->ownsTeam($this);
    }

    /**
     * Get all of the pending member invitations for the team.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Remove the given member from the team.
     */
    public function removeMember(BelongsToTeam $member): void
    {
        $this->members()->detach($member);

        $this->refresh('members');

        $member->refresh('teams');
    }

    /**
     * Purge all of the team's resources.
     */
    public function purge(): void
    {
        $this->members()->detach();

        $this->delete();
    }
}
