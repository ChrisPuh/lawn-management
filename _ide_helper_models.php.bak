<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $location
 * @property string|null $size
 * @property GrassSeed|null $grass_seed
 * @property GrassType|null $type
 * @property int $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 * @method static static make(array $attributes = [])
 * @method static static create(array $attributes = [])
 * @method static static forceCreate(array $attributes)
 * @method HasMany hasMany(string $related, string|null $foreignKey = null, string|null $localKey = null)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnMowing> $mowingRecords
 * @property-read int|null $mowing_records_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\LawnFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn forUser()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn whereGrassSeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lawn whereUserId($value)
 */
	final class Lawn extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Lawn|null $lawn
 * @method static \Database\Factories\LawnAeratingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating query()
 */
	class LawnAerating extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Lawn|null $lawn
 * @method static \Database\Factories\LawnFertilizingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing query()
 */
	class LawnFertilizing extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $imageable
 * @property-read \App\Models\Lawn|null $lawn
 * @method static \Database\Factories\LawnImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage query()
 */
	class LawnImage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $lawn_id
 * @property \Carbon\Carbon $mowed_on
 * @property string|null $cutting_height
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Lawn $lawn
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 * @method static \Database\Factories\LawnMowingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereCuttingHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereMowedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereUpdatedAt($value)
 */
	final class LawnMowing extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Lawn|null $lawn
 * @method static \Database\Factories\LawnScarifyingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying query()
 */
	class LawnScarifying extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lawn> $lawns
 * @property-read int|null $lawns_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	final class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

