# Guia de Teste WebSocket com Postman

Este guia descreve o passo a passo completo para testar a conexão WebSocket com canais privados usando o Postman.

## Pré-requisitos

- Postman instalado (versão com suporte a WebSocket)
- Servidor Laravel rodando (`php artisan serve`)
- Servidor WebSocket rodando (Soketi, Reverb ou Pusher)
- Banco de dados configurado com pelo menos um usuário e uma sala criada

---

## Visão Geral do Fluxo

```
1. Login (obter token)
      ↓
2. Criar/Obter sala (obter UUID)
      ↓
3. Conectar ao WebSocket
      ↓
4. Obter socket_id
      ↓
5. Autenticar canal privado
      ↓
6. Inscrever no canal com auth
      ↓
7. Disparar evento de teste
```

---

## Passo 1: Fazer Login

Primeiro, você precisa obter um token de autenticação.

### Request

```
POST http://localhost:8000/api/login
Content-Type: application/json
```

### Body

```json
{
    "email": "seu-email@exemplo.com",
    "password": "sua-senha"
}
```

### Response (exemplo)

```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Seu Nome",
            "email": "seu-email@exemplo.com"
        },
        "token": "1|abc123def456..."
    }
}
```

> ⚠️ **Importante**: Guarde o `token` retornado. Você precisará dele em todas as próximas requisições autenticadas.

---

## Passo 2: Obter UUID da Sala (Disparar Evento de Teste)

Esta rota cria/obtém uma sala e dispara eventos de teste no canal.

### Request

```
POST http://localhost:8000/api/tests/test-event-builder
```

> **Nota**: Esta rota não requer autenticação.

### Response (exemplo)

```json
{
    "success": true,
    "data": {
        "room": {
            "id": 1,
            "uuid": "a7e807d8-3e1a-4728-b95c-6308ebc81556",
            "name": "TestRoom",
            "status": "created"
        }
    }
}
```

> ⚠️ **Importante**: Guarde o `uuid` da sala. Este será o nome do canal: `private-room-{uuid}`

---

## Passo 3: Conectar ao Servidor WebSocket

No Postman, crie uma nova requisição WebSocket.

### URL de Conexão

```
ws://localhost:6001/app/app-key
```

> **Nota**: Substitua `app-key` pela sua `PUSHER_APP_KEY` configurada no `.env`

### Parâmetros de Query (opcional)

Se necessário, adicione:
- `protocol=7`
- `client=postman`
- `version=7.0`

URL completa:
```
ws://localhost:6001/app/app-key?protocol=7&client=postman&version=7.0
```

### Ao Conectar

Você receberá uma mensagem de confirmação:

```json
{
    "event": "pusher:connection_established",
    "data": "{\"socket_id\":\"123456.7890123\",\"activity_timeout\":120}"
}
```

> ⚠️ **Importante**: Guarde o `socket_id` do campo `data` (é uma string JSON, você precisa fazer parse). Este ID é necessário para autenticar o canal.

---

## Passo 4: Autenticar o Canal Privado

Antes de se inscrever em um canal privado, você precisa obter um token de autorização.

### Request

```
POST http://localhost:8000/api/broadcasting/auth
Authorization: Bearer {seu-token-do-passo-1}
Content-Type: application/json
```

### Body

```json
{
    "socket_id": "123456.7890123",
    "channel_name": "private-room-a7e807d8-3e1a-4728-b95c-6308ebc81556"
}
```

> **Nota**: 
> - `socket_id`: obtido no Passo 3
> - `channel_name`: `private-room-` + UUID obtido no Passo 2

### Response (exemplo)

```json
{
    "auth": "app-key:a1b2c3d4e5f6..."
}
```

> ⚠️ **Importante**: Guarde o valor de `auth`. Você precisará dele para se inscrever no canal.

---

## Passo 5: Inscrever no Canal Privado

De volta à conexão WebSocket no Postman, envie a seguinte mensagem:

### Mensagem WebSocket

```json
{
    "event": "pusher:subscribe",
    "data": {
        "channel": "private-room-a7e807d8-3e1a-4728-b95c-6308ebc81556",
        "auth": "app-key:a1b2c3d4e5f6..."
    }
}
```

> **Nota**: 
> - `channel`: mesmo nome usado no Passo 4
> - `auth`: valor obtido no Passo 4

### Response de Sucesso

```json
{
    "event": "pusher:subscription_succeeded",
    "channel": "private-room-a7e807d8-3e1a-4728-b95c-6308ebc81556",
    "data": {}
}
```

### Response de Erro (se algo estiver errado)

```json
{
    "event": "pusher:subscription_error",
    "channel": "private-room-a7e807d8-3e1a-4728-b95c-6308ebc81556",
    "data": {
        "type": "AuthError",
        "error": "The connection is unauthorized.",
        "status": 401
    }
}
```

---

## Passo 6: Disparar Eventos de Teste

Agora que você está inscrito no canal, dispare eventos para testar:

### Request

```
POST http://localhost:8000/api/tests/test-event-builder
```

### O que esperar

Após enviar esta requisição, você deverá receber eventos no WebSocket:

1. **GameStartingEvent** (após 1 segundo):
```json
{
    "event": "game-starting",
    "channel": "private-room-a7e807d8-3e1a-4728-b95c-6308ebc81556",
    "data": { ... }
}
```

2. **ClockTickEvent** (5 vezes, a cada 1 segundo):
```json
{
    "event": "clock-tick",
    "channel": "private-room-a7e807d8-3e1a-4728-b95c-6308ebc81556",
    "data": { ... }
}
```

---

## Resumo Rápido

| Passo | Ação | Endpoint/URL |
|-------|------|--------------|
| 1 | Login | `POST /api/login` |
| 2 | Obter sala | `POST /api/tests/test-event-builder` |
| 3 | Conectar WS | `ws://localhost:6001/app/{APP_KEY}` |
| 4 | Auth canal | `POST /api/broadcasting/auth` |
| 5 | Subscribe | Enviar JSON no WS |
| 6 | Testar | `POST /api/tests/test-event-builder` |

---

## Troubleshooting

### Erro: "The connection is unauthorized" (401)

- Verifique se o `socket_id` está correto
- Verifique se o `channel_name` está no formato `private-room-{uuid}`
- Verifique se o token Bearer está correto na requisição de auth
- Verifique se o usuário está autenticado

### Erro: Conexão WebSocket recusada

- Verifique se o servidor WebSocket está rodando
- Verifique a porta (geralmente 6001 para Soketi/Laravel Websockets)
- Verifique se a `APP_KEY` está correta

### Erro: Canal não encontrado

- Verifique se o canal está definido em `routes/channels.php`
- O formato deve ser `private-room-{uuid}` onde o channel é definido como `room-{uuid}`

---

## Configuração do Servidor WebSocket

### Variáveis de Ambiente (.env)

```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=app-id
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

### Iniciar Soketi (se usando)

```bash
soketi start
```

### Iniciar Laravel Reverb (se usando)

```bash
php artisan reverb:start
```
