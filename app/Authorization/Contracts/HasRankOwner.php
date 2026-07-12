<?php

namespace App\Authorization\Contracts;

interface HasRankOwner
{
    /**
     * Get the User instance that is the "rank owner" of this entity.
     * Often this returns $this if the entity is a User, or $this->user if it's something else.
     *
     * @return \App\Models\User|null
     */
    public function getRankOwner();
}
