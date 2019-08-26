<?php

namespace LdapRecord\Models\ActiveDirectory;

use LdapRecord\Models\Concerns\HasGroups;
use LdapRecord\Models\Concerns\HasPassword;
use LdapRecord\Models\Concerns\CanAuthenticate;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Entry implements Authenticatable
{
    use HasGroups, HasPassword, CanAuthenticate;

    /**
     * The object classes of the LDAP model.
     * 
     * @var array
     */
    public static $objectClasses = [
        'top',
        'person',
        'organizationalperson',
        'user',
    ];

    /**
     * The groups relationship.
     *
     * Retrieves groups that the current user is apart of.
     *
     * @return \LdapRecord\Models\Relations\HasMany
     */
    public function groups()
    {
        return $this->hasMany(Group::class, 'member');
    }

    /**
     * Retrieves the primary group of the current user.
     *
     * @return Group|null
     */
    public function getPrimaryGroup()
    {
        $groupSid = preg_replace('/\d+$/', $this->getFirstAttribute('primarygroupid'), $this->getConvertedSid());

        $model = reset($this->groups()->getRelated());

        return $this->newQueryWithoutScopes()->setModel(new $model)->findBySid($groupSid);
    }
}