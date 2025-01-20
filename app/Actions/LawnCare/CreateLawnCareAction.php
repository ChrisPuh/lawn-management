<?php

declare(strict_types=1);

namespace App\Actions\LawnCare;

use App\Contracts\LawnCare\CreateLawnCareActionContract;
use App\Contracts\LawnCare\LawnCareActionContract;
use App\DataObjects\LawnCare\BaseLawnCareData;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;
use InvalidArgumentException;

final class CreateLawnCareAction implements CreateLawnCareActionContract
{
    /**
     * @var array<string, LawnCareActionContract>
     */
    private array $actions;

    public function __construct(
        CreateMowingAction $mowAction,
        CreateFertilizingAction $fertilizeAction,
        CreateWateringAction $waterAction
    ) {
        $this->actions = [
            LawnCareType::MOW->value => $mowAction,
            LawnCareType::FERTILIZE->value => $fertilizeAction,
            LawnCareType::WATER->value => $waterAction,
        ];
    }

    public function execute(LawnCareType $type, BaseLawnCareData $data): LawnCare
    {
        $action = $this->actions[$type->value] ?? null;

        if ($action === null) {
            throw new InvalidArgumentException("Unsupported lawn care action type: {$type->value}");
        }

        return $action->execute($data);
    }


}
