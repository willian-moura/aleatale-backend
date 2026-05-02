<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ATurnEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;

class VotingResultEvent extends ATurnEvent
{
    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::VOTING_RESULT;
    }

    public function getPayload(): array
    {
        $mostVotedSentence = $this->turn->sentences()->withCount('votes')->orderBy('votes_count', 'desc')->first();
        if (!$mostVotedSentence) {
            return [
                ...parent::getPayload(),
                'most_voted_sentence_votes' => 0,
                'most_voted_sentence_id' => null,
                'most_voted_sentence_content' => null,
                'most_voted_sentence_user_id' => null,
            ];
        }

        $taleContent = $this->getRoom()->tale_content;
        $taleContent .= ' ' . $mostVotedSentence->content;
        $this->getRoom()->update([
            'tale_content' => $taleContent,
        ]);

        return [
            ...parent::getPayload(),
            'most_voted_sentence_votes' => $mostVotedSentence->votes_count,
            'most_voted_sentence_id' => $mostVotedSentence->id,
            'most_voted_sentence_content' => $mostVotedSentence->content,
            'most_voted_sentence_user_id' => $mostVotedSentence->user_id,
        ];
    }
}
