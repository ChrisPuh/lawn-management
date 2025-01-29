<?php

declare(strict_types=1);

namespace App\Actions\LawnCare;

use App\Contracts\LawnCare\UpdateLawnCareActionContract;
use App\DataObjects\LawnCare\BaseLawnCareData;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;
use InvalidArgumentException;

final class UpdateLawnCareAction implements UpdateLawnCareActionContract
{
    /**
     * @var array<string, UpdateMowingAction|UpdateFertilizingAction|UpdateWateringAction>
     */
    private array $actions;

    public function __construct(
        UpdateMowingAction $mowAction,
        UpdateFertilizingAction $fertilizeAction,
        UpdateWateringAction $waterAction
    ) {
        $this->actions = [
            LawnCareType::MOW->value => $mowAction,
            LawnCareType::FERTILIZE->value => $fertilizeAction,
            LawnCareType::WATER->value => $waterAction,
        ];
    }

    public function execute(LawnCare $lawnCare, LawnCareType $type, BaseLawnCareData $data): LawnCare
    {
        $action = $this->actions[$type->value] ?? null;

        if ($action === null) {
            throw new InvalidArgumentException("Unsupported lawn care action type: {$type->value}");
        }

        return $action->execute($lawnCare, $data);
    }
}
