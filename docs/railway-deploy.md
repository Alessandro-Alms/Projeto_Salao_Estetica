# Deploy na Railway

Este projeto Laravel fica dentro da pasta `src`, entao na Railway o servico deve usar `Root Directory = /src`.

## Arquivos preparados

- `src/railway.toml`: configura healthcheck em `/up` e executa `php artisan migrate --force` antes do deploy.
- `src/.env.railway.example`: modelo das variaveis de ambiente para producao.

## Passos no painel da Railway

1. Envie este repositorio para o GitHub.
2. Crie um projeto na Railway com `Deploy from GitHub repo`.
3. No servico, configure `Root Directory` como `/src`.
4. Adicione um banco `Postgres` no projeto.
5. Em `Variables`, copie os valores de `src/.env.railway.example` e preencha:
   - `APP_KEY`
   - `APP_URL`
   - `DB_HOST`
   - `DB_PORT`
   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`
6. Em `Networking`, gere o dominio publico.
7. Faça um redeploy.

## Gerar APP_KEY

No terminal, dentro da pasta `src`:

```powershell
php artisan key:generate --show
```

## Observacoes

- A aplicacao ja possui endpoint de healthcheck em `/up`.
- Nao use `sqlite` em producao na Railway; prefira `Postgres`.
- O comando de pre-deploy vai rodar as migracoes automaticamente.
