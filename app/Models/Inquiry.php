<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'type',
        'name',
        'email',
        'phone',
        'event_date',
        'guests',
        'notes',
        'flavour',
        'size',
        'message_on_cake',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'guests' => 'integer',
            'is_read' => 'boolean',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'services' => 'Catering / services',
            'customization' => 'Custom cake request',
            'newsletter' => 'Newsletter signup',
            default => ucfirst($this->type),
        };
    }

    public function summary(): string
    {
        $parts = array_filter([
            $this->name,
            $this->flavour,
            $this->size,
            $this->notes ? \Illuminate\Support\Str::limit($this->notes, 80) : null,
        ]);

        return implode(' · ', $parts) ?: $this->email;
    }
}
