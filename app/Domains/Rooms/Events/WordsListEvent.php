<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ATurnEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;
use App\Models\Sentence;

class WordsListEvent extends ATurnEvent
{
    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::WORDS_LIST;
    }

    public function getPayload(): array
    {
        $sentences = $this->turn->sentences()->get();
        return [
            ...parent::getPayload(),
            'sentences' => $sentences
        ];
    }
}
