# Documentação da API de pagamentos

#### ` Tecnologia utilizad: `
`PHP`
`Laravel`
`MySQL`

#### `Observações Importantes: `
`O usuario padrão possui o seguinte email e senha: `
 - `email: user@example.com`
- `senha: senha#123`
 
`Os testes encontram-se ao final desse documento`
`Faça o .env de acordo com o exemplo em .env.example`

Para que a API funcione corretamente você deve executar dentro do projeto os seguintes comandos:
- php artisan jwt:secret
- php artisan migrate
- php artisan db:seed

Eles são necessários para o funcionamento correto da API
## Endpoint 1: Login

### Descrição
Este endpoint permite que os usuários façam login na aplicação e obtenham o token JWT para acessar os outros endpoints.

### Método
`POST`

### URL
`/api/login`

### Corpo da Solicitação (Body)
```json
{
    "email": "user@example.com",
    "password": "senha#123"
}
```

### Resposta de Sucesso

- **Código:** 200 OK
- **Corpo da Resposta:**
```json
{
    "user": "usuário 1",
    "saldo": 0,
    "token": (apenas um exemplo de token) "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzEzDgyMjk5LCJleHAiOjE3MTM0ODU4OTksIm5iZiI6MTcxMzQ4MjI5OSwianRpIjoib3I3SFhVS2FBV1F0UlV6cCIInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.k4299pltMi6T5JDdTjSkN8UjSD97L3kZfGEL0hLPlw",
    "token_type": "bearer",
    "expires_in": 3600
}
```

### Respostas de Erro
##### Em caso de credenciais inválidas:
- **Código:** 400 Bad Request
- **Corpo da Resposta:**
```json
{
    "message": "credenciais inválidas"
}
```

##### Em caso de falta de credencial:
- **Código:** 400 Bad Request
- **Corpo da Resposta:**
```json
{
    "email": [
        "The email field is required."
    ],
    "password": [
        "The password field is required."
    ]
}
```
##### Em caso de formato inválido de email:
- **Código:** 400 Bad Request
- **Corpo da Resposta:**
```json
{
    "email": [
        "The email field must be a valid email address."
    ]
}
```
## Endpoint 2: Payments

### Descrição
Este endpoint permite o registro de novos pagamentos na aplicação.

### Método
`POST`

### URL
`/api/payments`

### Header Requerido
```json
{
    "Authorization": "Bearer (token gerado no login)"
}
```

### Corpo da Solicitação (Body)
```json
{
    "nome_cliente": "nome do cliente",
    "cpf": "cpf do cliente",
    "descricao": "descrição do pagamento",
    "valor": valor do pagamento (float),
    "status": "(pending, paid, expired, failed)",
    "payment_method": "(pix, boleto, bank_transfer)",
    "data_pagamento": "YYYY-MM-DD"
}
```

### Resposta de Sucesso

- **Código:** 201 Created
- **Corpo da Resposta:**
```json
{
    "message": "Novo pagamento registrado com sucesso!"
}
```

### Resposta de Erro

- **Código:** 400 Bad Request
- **Corpo da Resposta:**
```json
{
    "field": [
        "field error"
    ],
    "field": [
        "field error"
    ],
    [...]
}
```

### Resposta de Token Inválido ou Faltando

- **Código:** 401 Unauthorized
- **Corpo da Resposta:**
```json
{
    "message": "Unauthenticated."
}
```
## Endpoint 3: Pagamentos

### Descrição
Este endpoint permite ter um resumo de todos pagamentos registrados.

### Método
`GET`

### URL
`/api/payments`

### Header Requerido
```json
{
    "Authorization": "Bearer (token gerado no login)"
}
```

### Resposta de Sucesso

    - **Código:** 200
    - **Resposta:**
      ```json
      {
          "ID": "id do pagamento",
          "Nome do cliente": "nome do cliente",
          "Valor": "valor do pagamento",
          "Status": "status do pagamento"
          "Data do pagamento": "YYYY-MM-DD"
      },
      {...}
      ```

### Resposta de Token Inválido ou Faltando

- **Código:** 401 Unauthorized
- **Corpo da Resposta:**
```json
{
    "message": "Unauthenticated."
}
```

# Endpoint 4: Payments/{id}

## Descrição
Este endpoint permite obter os detalhes do pagamento escolhido.

### Método
`GET`

### URL
`/api/payments/{id}`

### Header Requerido
```json
{
    "Authorization": "Bearer (token gerado no login)"
}
```
## Resultados

### Em caso de sucesso:
- Código: 200
- Resposta:
    ```
    {
        "Id": 1,
        "Nome do cliente": "nome do cliente",
        "CPF": "cpf do cliente",
        "Descrição": "descrição do pagamento",
        "Valor": valor do pagamento,
        "Status": "status do pagamento",
        "Payment Method": "slug do método de pagamento",
        "Data de pagamento": "YYYY-MM-DD"
    }
    ```

### Em caso de token faltando ou inválido:
- Código: 401
- Resposta:
    ```
    {
        "message": "Unauthenticated."
    }
    ```
## Endpoint 5: Payments/Proccess

## Descrição
Este endpoint permite processar o pagamento pagamento escolhido.

### Método
`POST`

### URL
`/api/payments/{id}`

### Header Requerido
```json
{
    "Authorization": "Bearer (token gerado no login)"
}
```

### Corpo da Requisição
```json
{
    "payment_id": 2
}
```

### Resultados

#### Em caso de pagamento aprovado:
- **Código:** 200
- **Resposta:**
```json
{
    "status": "O pagamento foi aprovado",
    "saldo": "O saldo atual é de: R$102"
}
```

#### Em caso de pagamento recusado:
- **Código:** 400
- **Resposta:**
```json
{
    "status": "O pagamento foi recusado",
    "saldo": "O saldo atual é de: R$0"
}
```

#### Em caso de pagamento expirado:
- **Código:** 303
- **Resposta:**
```json
{
    "message": "Esse pagamento está expirado"
}
```

#### Em caso de pagamento já processado:
- **Código:** 303
- **Resposta:**
```json
{
    "message": "Esse pagamento já foi processado"
}
```

#### Em caso de token faltando ou inválido:
- **Código:** 401
- **Resposta:**
```json
{
    "message": "Unauthenticated."
}
```

***


# Teste do endpoint 2: payments

## Método
- **POST**

## Header
```json
{
	“Authorization” : “Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzEzNDg2NTYwLCJleHAiOjE3MTM0OTAxNjAsIm5iZiI6MTcxMzQ4NjU2MCwianRpIjoidGdUSlJiRmE0RWdYRmFPZiIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.oFprvIk0qZQaWH7VWoNTKYp-crYKFvd2AvM9Ntup6tY
”
}
```

## Body
```json
{
    "nome_cliente": "João Miguel",
    "cpf": "11223344556",
    "descricao": "Reforma da cozinha e instalação de ar-condicionado na sala de estar",
    "valor": 6000.50,
    "status":"pending",
    "payment_method": "bank_transfer",
    "data_pagamento": "2024-05-01"
}
```

## Resultado
- **Código**: 201

- **Resposta**
```json
{
    "message": "Novo pagamento registrado com sucesso!"
}
```

# Teste do endpoint 3: payments

## Método
- **GET**

## Header
```json
{
	“Authorization” : “Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzEzNDg2NTYwLCJleHAiOjE3MTM0OTAxNjAsIm5iZiI6MTcxMzQ4NjU2MCwianRpIjoidGdUSlJiRmE0RWdYRmFPZiIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.oFprvIk0qZQaWH7VWoNTKYp-crYKFvd2AvM9Ntup6tY
”
}
```

## Resultado
- **Código**: 200

- **Resposta**
```json
{
    "ID": 1,
    "Nome do cliente": "João Miguel",
    "Valor": 6000.5,
    "Status": "pending",
    "Data do pagamento": "2024-05-01"
}
```
# Teste do endpoint 4: payments/1

## Método
- **GET**

## Header
```json
{
	“Authorization” : “Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzEzNDg2NTYwLCJleHAiOjE3MTM0OTAxNjAsIm5iZiI6MTcxMzQ4NjU2MCwianRpIjoidGdUSlJiRmE0RWdYRmFPZiIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.oFprvIk0qZQaWH7VWoNTKYp-crYKFvd2AvM9Ntup6tY
”
}
```

## Resultado
- **Código**: 200

- **Resposta**
```json
{
    "Id": 1,
    "Nome do cliente": "João Miguel",
    "CPF": "11223344556",
    "Descrição": "Reforma da cozinha e instalação de ar-condicionado na sala de estar",
    "Valor": 6000.5,
    "Status": "pending",
    "Payment Method": "bank_transfer",
    "Data de pagamento": "2024-05-01"
}
```