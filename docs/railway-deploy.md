# Deploy na Railway

Este projeto Laravel fica dentro da pasta `src`, entao na Railway o servico deve usar `Root Directory = /src`.

## Arquivos preparados

- `src/railway.toml`: configura build, start command, healthcheck em `/up` e executa `php artisan migrate --force` antes do deploy.
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
   - `CONTACT_TO_ADDRESS`
6. Mantenha `APP_ENV=production` e `APP_DEBUG=false`.
7. Em `Networking`, gere o dominio publico e use esse dominio em `APP_URL`.
8. Faca um redeploy.

## Gerar APP_KEY

No terminal, dentro da pasta `src`:

```powershell
php artisan key:generate --show
```

## Observacoes

- A aplicacao ja possui endpoint de healthcheck em `/up`.
- Nao use `sqlite` em producao na Railway; prefira `Postgres`.
- O comando de pre-deploy vai rodar as migracoes automaticamente.
- O build nao usa `route:cache` porque o projeto ainda possui rotas com closures.
- Deixe `SEED_ON_DEPLOY=false` em producao para nao apagar dados.
- Use `ADMIN_SEED_ON_DEPLOY=true` apenas quando quiser criar/atualizar o gerente inicial pelas variaveis `ADMIN_*`.
