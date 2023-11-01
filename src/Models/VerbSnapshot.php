<?php

namespace Thunk\Verbs\Models;

use Illuminate\Database\Eloquent\Model;
use Thunk\Verbs\State;
use Thunk\Verbs\Support\StateSerializer;

class VerbSnapshot extends Model
{
    public $table = 'verb_snapshots';

    public $guarded = [];

    protected ?State $state = null;

    public function state(): State
    {
        $this->state ??= app(StateSerializer::class)->deserialize($this->type, $this->data);
        $this->state->id = $this->id;
        $this->state->last_event_id = $this->last_event_id;

        return $this->state;
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWhereDataContains($query, array $data)
    {
        return $query->whereJsonContains('data', $data);
    }
}
