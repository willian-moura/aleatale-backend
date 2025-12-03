# Alternativas de Comunicação em Tempo Real com WebSocket

Documentação de abordagens e melhores práticas para a comunicação em tempo real do jogo Aleatale - um jogo colaborativo de criação de histórias baseado em salas e turnos.

---

## 🎮 Resumo do Jogo

- Jogadores conectam em uma sala a partir da lista de salas
- Após todos os jogadores darem "pronto", em 5 segundos o jogo é iniciado para a sala em questão
- O jogo funciona em turnos de 2 etapas:
  - **Etapa 1 (10 segundos)**: todos os participantes da sala enviam uma palavra aleatória
  - **Intervalo (5 segundos)**: pausa entre as etapas
  - **Etapa 2 (10 segundos)**: jogadores votam em uma das palavras enviadas (a própria palavra não aparece na lista)
  - **Resultado (10 segundos)**: exibe a palavra vencedora e o nome do jogador que enviou
- Os turnos duram até o contador geral atingir a meta de tempo da sala (5 minutos)
- No final, os participantes podem copiar o texto/história gerado colaborativamente

---

## 🏗️ Arquiteturas de Comunicação

### 1. Arquitetura Centralizada (Server-Authoritative)

**Recomendada para este caso.**

```
┌─────────────────────────────────────────────────────────┐
│                      SERVIDOR                           │
│  - Mantém o estado autoritativo do jogo                │
│  - Controla todos os timers                            │
│  - Valida e processa todas as ações                    │
│  - Broadcast de eventos para todos os clientes         │
└─────────────────────────────────────────────────────────┘
         ▲           ▲           ▲           ▲
         │           │           │           │
    ┌────┴───┐  ┌────┴───┐  ┌────┴───┐  ┌────┴───┐
    │Cliente1│  │Cliente2│  │Cliente3│  │Cliente4│
    └────────┘  └────────┘  └────────┘  └────────┘
```

**Vantagens:**
- Estado sempre sincronizado
- Evita trapaças (cheating)
- Timers precisos controlados pelo servidor
- Único ponto de verdade (single source of truth)

**Desvantagens:**
- Maior latência percebida
- Mais carga no servidor

---

### 2. Arquitetura Híbrida (Optimistic Updates)

O cliente faz atualizações locais imediatas e o servidor confirma/corrige depois.

**Vantagens:**
- Interface mais responsiva
- Melhor UX em conexões lentas

**Desvantagens:**
- Mais complexidade no código
- Possíveis "rollbacks" visuais

---

## 📨 Padrões de Mensagens JSON

### Alternativa A: Mensagens Tipadas com Ação

```json
{
  "type": "PLAYER_READY",
  "payload": {
    "playerId": "uuid",
    "roomId": "uuid"
  },
  "timestamp": 1701619200000
}
```

```json
{
  "type": "WORD_SUBMITTED",
  "payload": {
    "word": "cachorro",
    "playerId": "uuid"
  }
}
```

```json
{
  "type": "VOTE_CAST",
  "payload": {
    "wordId": "uuid",
    "playerId": "uuid"
  }
}
```

### Alternativa B: Padrão Request/Response com Channels

```json
{
  "channel": "room.123",
  "event": "client.word_submit",
  "data": { "word": "gato" },
  "requestId": "abc123"
}
```

```json
{
  "channel": "room.123", 
  "event": "server.word_accepted",
  "data": { "wordId": "xyz", "word": "gato" },
  "requestId": "abc123"
}
```

---

## ⏱️ Estratégias de Sincronização de Tempo

### Opção 1: Timer Centralizado no Servidor (Recomendado)

O servidor é a única fonte de verdade para o tempo:

```json
{
  "type": "TIMER_SYNC",
  "payload": {
    "phase": "WORD_SUBMISSION",
    "remainingMs": 8500,
    "serverTime": 1701619200000
  }
}
```

**Fluxo:**
1. Servidor controla os timers
2. Servidor envia eventos de mudança de fase
3. Cliente exibe countdown baseado no `remainingMs`
4. Cliente pode fazer interpolação local entre syncs

### Opção 2: Timestamp + Offset

Servidor envia timestamps absolutos e cliente calcula o offset:

```json
{
  "type": "PHASE_START",
  "payload": {
    "phase": "VOTING",
    "endsAt": 1701619210000,
    "serverTime": 1701619200000
  }
}
```

Cliente calcula: `offset = serverTime - clientTime` e ajusta o timer local.

---

## 🎮 Estrutura de Eventos do Jogo

### Eventos do Servidor → Cliente (Broadcasts)

| Evento | Descrição |
|--------|-----------|
| `ROOM_STATE_UPDATE` | Estado completo da sala (para sync inicial ou reconexão) |
| `PLAYER_JOINED` | Novo jogador entrou |
| `PLAYER_LEFT` | Jogador saiu |
| `PLAYER_READY` | Jogador marcou pronto |
| `GAME_STARTING` | Countdown de 5s iniciado |
| `PHASE_CHANGE` | Mudança de fase (submissão → intervalo → votação → resultado) |
| `TIMER_TICK` | Sync de timer (opcional, pode ser a cada 1-2s) |
| `WORDS_LIST` | Lista de palavras para votação |
| `VOTING_RESULT` | Palavra vencedora do turno |
| `STORY_UPDATE` | Nova palavra adicionada à história |
| `GAME_END` | Jogo finalizado, história completa |

### Eventos do Cliente → Servidor

| Evento | Descrição |
|--------|-----------|
| `JOIN_ROOM` | Entrar na sala |
| `LEAVE_ROOM` | Sair da sala |
| `TOGGLE_READY` | Marcar/desmarcar pronto |
| `SUBMIT_WORD` | Enviar palavra |
| `CAST_VOTE` | Votar em uma palavra |

---

## 🔄 Gerenciamento de Estado

### Máquina de Estados da Sala

```
WAITING → COUNTDOWN → WORD_PHASE → INTERVAL → VOTE_PHASE → RESULT → WORD_PHASE...
                                                              ↓
                                                          GAME_END
```

### Estrutura do Estado da Sala

```json
{
  "roomId": "uuid",
  "status": "WORD_PHASE",
  "currentTurn": 3,
  "phase": {
    "name": "WORD_SUBMISSION",
    "endsAt": 1701619210000,
    "remainingMs": 8000
  },
  "players": [
    { "id": "uuid", "name": "João", "ready": true, "hasSubmitted": true },
    { "id": "uuid", "name": "Maria", "ready": true, "hasSubmitted": false }
  ],
  "story": ["Era", "uma", "vez", "um"],
  "gameEndsAt": 1701619500000
}
```

---

## 🛡️ Melhores Práticas

### 1. Reconexão Automática
- Cliente deve implementar reconnect com backoff exponencial
- Servidor deve enviar estado completo (`ROOM_STATE_UPDATE`) ao reconectar

### 2. Heartbeat/Ping-Pong

```json
// Cliente → Servidor
{ "type": "PING", "timestamp": 1701619200000 }

// Servidor → Cliente  
{ "type": "PONG", "timestamp": 1701619200050 }
```

### 3. Validação Server-Side
- Nunca confiar em dados do cliente
- Validar se jogador pode votar/enviar palavra na fase atual
- Validar se o tempo ainda não expirou

### 4. Idempotência
- Usar `requestId` ou `messageId` para evitar processamento duplicado

### 5. Presença e Timeout
- Detectar jogadores inativos/desconectados
- Remover jogadores após X segundos sem heartbeat

---

## 📊 Comparativo das Abordagens

| Aspecto | Centralizada | Híbrida |
|---------|--------------|---------|
| Complexidade | Menor | Maior |
| Consistência | ✅ Garantida | ⚠️ Eventual |
| Responsividade | Depende da latência | ✅ Imediata |
| Anti-cheat | ✅ Forte | ⚠️ Fraca |
| Para este jogo | **Recomendada** | Opcional |

---

## 🎯 Recomendação Final

Para um jogo estilo Gartic/Aleatale, recomenda-se:

1. **Arquitetura Centralizada** - o servidor é a autoridade absoluta
2. **Mensagens tipadas com payload** (Alternativa A)
3. **Timer controlado pelo servidor** com syncs periódicos
4. **Máquina de estados** clara para as fases do jogo
5. **Broadcast para todos os jogadores** a cada mudança de estado relevante

Essa abordagem garante que todos os jogadores vejam exatamente a mesma coisa ao mesmo tempo, o que é essencial para um jogo de turnos com votação.

---

*Documento gerado em: Dezembro 2024*

