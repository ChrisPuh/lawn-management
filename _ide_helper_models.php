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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnMowing> $mowingRecords
 * @property-read int|null $mowing_records_count
 * @property-read User $user
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnAerating> $aeratingRecords
 * @property-read int|null $aerating_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnFertilizing> $fertilizingRecords
 * @property-read int|null $fertilizing_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnScarifying> $scarifyingRecords
 * @property-read int|null $scarifying_records_count
 * @mixin \Eloquent
 */
	final class Lawn extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnImage> $images
 * @property-read int|null $images_count
 * @property-read Lawn $lawn
 * @method static \Database\Factories\LawnAeratingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating query()
 * @property int $id
 * @property int $lawn_id
 * @property \Illuminate\Support\Carbon $aerated_on
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereAeratedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereUpdatedAt($value)
 */
	final class LawnAerating extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $lawn_id
 * @property int $created_by_id
 * @property \App\Enums\LawnCare\LawnCareType $type
 * @property array<array-key, mixed>|null $care_data
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $performed_at
 * @property \Illuminate\Support\Carbon|null $scheduled_for
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\Lawn $lawn
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare completed()
 * @method static \Database\Factories\LawnCareFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare forLawn(\App\Models\Lawn|int $lawn)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare scheduled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereCareData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare wherePerformedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereScheduledFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCare whereUpdatedAt($value)
 */
	final class LawnCare extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $lawn_care_id
 * @property int $user_id
 * @property string $action
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LawnCare $lawnCare
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog createdBetween(\DateTime $start, \DateTime $end)
 * @method static \Database\Factories\LawnCareLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog forAction(string $action)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog forLawnCare(\App\Models\LawnCare|int $lawnCare)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog whereLawnCareId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnCareLog whereUserId($value)
 */
	final class LawnCareLog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnImage> $images
 * @property-read int|null $images_count
 * @property-read Lawn $lawn
 * @method static \Database\Factories\LawnFertilizingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing query()
 * @property int $id
 * @property int $lawn_id
 * @property \Illuminate\Support\Carbon|null $fertilized_on
 * @property string|null $fertilizer_name
 * @property string|null $quantity
 * @property string|null $quantity_unit
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing whereFertilizedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing whereFertilizerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing whereQuantityUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing whereUpdatedAt($value)
 */
	final class LawnFertilizing extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $lawn_id
 * @property string $image_path
 * @property string $imageable_type
 * @property int $imageable_id
 * @property LawnImageType $type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property \Illuminate\Support\Carbon|null $delete_after
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Lawn $lawn
 * @property-read LawnAerating|LawnMowing|LawnScarifying|LawnFertilizing $imageable
 * @template TModel of LawnAerating|LawnMowing|LawnScarifying|LawnFertilizing
 * @method static \Database\Factories\LawnImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereArchivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereDeleteAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereImageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereImageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereUpdatedAt($value)
 */
	final class LawnImage extends \Eloquent {}
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnImage> $images
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereNotes($value)
 * @property string|null $notes
 * @property-read int|null $images_count
 */
	final class LawnMowing extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $lawn_id
 * @property \Carbon\Carbon $scarified_on
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Lawn $lawn
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnImage> $images
 * @property-read int|null $images_count
 * @method static \Database\Factories\LawnScarifyingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereScarifiedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	final class LawnScarifying extends \Eloquent {}
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Lawn> $lawns
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

