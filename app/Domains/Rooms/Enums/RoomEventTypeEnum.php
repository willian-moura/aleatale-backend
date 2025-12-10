<?php

namespace App\Domains\Rooms\Enums;

enum RoomEventTypeEnum: string
{
    /**
     * Estado completo da sala (para sync inicial ou reconexão)
     */
    case ROOM_STATE_UPDATE = 'room_state_update';

    /**
     * Novo jogador entrou
     */
    case PLAYER_JOINED = 'player_joined';

    /**
     * Jogador saiu
     */
    case PLAYER_LEFT = 'player_left';

    /**
     * Jogador marcou pronto
     */
    case PLAYER_READY = 'player_ready';

    /**
     * Countdown de 5s iniciado
     */
    case GAME_STARTING = 'game_starting';

    /**
     * Mudança de fase (submissão → intervalo → votação → resultado)
     */
    case PHASE_CHANGE = 'phase_change';

    /**
     * Sync de timer (opcional, pode ser a cada 1-2s)
     */
    case CLOCK_TICK = 'clock_tick';

    /**
     * Lista de palavras para votação
     */
    case WORDS_LIST = 'words_list';

    /**
     * Palavra vencedora do turno
     */
    case VOTING_RESULT = 'voting_result';

    /**
     * Nova palavra adicionada à história
     */
    case STORY_UPDATE = 'story_update';

    /**
     * Jogo finalizado, história completa
     */
    case GAME_END = 'game_end';
}
