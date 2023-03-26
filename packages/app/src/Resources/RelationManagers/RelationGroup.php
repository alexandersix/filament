<?php

namespace Filament\Resources\RelationManagers;

use Closure;
use Filament\Support\Components\Component;
use Illuminate\Database\Eloquent\Model;

class RelationGroup extends Component
{
    protected string | Closure | null $icon = null;

    protected string | Closure | null $badge = null;

    protected ?Model $ownerRecord = null;

    protected ?string $pageClass = null;

    /**
     * @param  array<class-string>  $managers
     */
    public function __construct(
        protected string | Closure $label,
        protected array | Closure $managers,
    ) {
    }

    /**
     * @param  array<class-string>  $managers
     */
    public static function make(string | Closure $label, array | Closure $managers): static
    {
        $static = app(static::class, ['label' => $label, 'managers' => $managers]);
        $static->configure();

        return $static;
    }

    public function ownerRecord(?Model $record): static
    {
        $this->ownerRecord = $record;

        return $this;
    }

    public function pageClass(?string $class): static
    {
        $this->pageClass = $class;

        return $this;
    }

    public function badge(string | Closure | null $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->evaluate($this->label);
    }

    /**
     * @return array<class-string>
     */
    public function getManagers(): array
    {
        $ownerRecord = $this->getOwnerRecord();
        $pageClass = $this->getPageClass();

        if (! ($ownerRecord && $pageClass)) {
            return $this->managers;
        }

        return array_filter(
            $this->managers,
            fn (string $manager): bool => $manager::canViewForRecord($ownerRecord, $pageClass),
        );
    }

    public function getBadge(): ?string
    {
        return $this->evaluate($this->badge);
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getOwnerRecord(): ?Model
    {
        return $this->ownerRecord;
    }

    public function getPageClass(): ?string
    {
        return $this->pageClass;
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return [
            'ownerRecord' => fn (): ?Model => $this->getOwnerRecord(),
            'pageClass' => fn (): ?string => $this->getPageClass(),
        ];
    }
}
