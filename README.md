# LOGIN 

## POST /login

```json
{
    "email": "test$gmail.com",
    "password": "123"
}
```
Retorna 
```json
{
	"token": "4|OqUpnJvnxSbAJeR8CLJwz0keYOFIaDXmrLwBZWjZ5b7eff6d"
}

```

## POST /new-user
```json
{
	"full_name": "Jonh Doe",
	"email": "test@gmail.com",
	"updated_at": "2025-06-21T15:10:06.000000Z",
	"created_at": "2025-06-21T15:10:06.000000Z",
	"id": 20
}   
```

## POST /seller/become
Tem que passar o token do usuário atual autenticado.
```json
{
	"store_name": "Nova loja",
}   
```

## POST /seller/product
Tem que passar o token do usuário atual autenticado.
```json
{
	"product": {
		"name": "Vai corinthians",
		"description": "Para assistir seu jogo....",
		"price": 270.56,
		"category_id": 1
	},
	"attributes": [
		{
			"attribute_id": 1,
			"attribute_option": 1
		}
	]
} 
```

## POST /seller/become
Tem que passar o token do usuário atual autenticado.
```json
{
	"store_name": "Nova loja",
}   
```

## POST /seller/product/detail/{id}
Tem que passar o token do usuário atual autenticado.
Retorna os detalhes do produto.


## GET /attributes/category/{id}
Retorna os atributos da categoria com as opções