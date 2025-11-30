# App Locadora de Carros — Remember

> Aplicação web desenvolvida em **Laravel** para gestão e locação de veículos.  
O projeto representa uma **evolução de um sistema anterior**, com foco em **boas práticas, arquitetura limpa e cobertura de testes automatizados**.

---

## Tecnologias Principais

| Categoria | Tecnologia |
|------------|-------------|
| Framework | Laravel (PHP) |
| Banco de Dados | MySQL |
| Container | Docker (com MySQL) |
| Servidor HTTP | FrankenPHP (Octane) |
| Autenticação | Sanctum |
| Monitoramento | Laravel Telescope |
| Testes | Pest |
| Documentação | Scribe |

## Pré-requisitos

- PHP 8.2+
- Node.js 22.13.5+

## Instalação e execução

### 1. Clone o repositório

```bash
git clone https://github.com/andredw13L/app_locadora_carros.git
cd app_locadora_carros
```

### 2. Instale as dependências PHP

```bash
composer install
```

### 3. Instale as dependências npm (Frontend)

```bash
npm install
```

### 4. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Suba o container com o banco de dados

```bash
docker-compose up -d
```

### 6. Configure o banco de dados

Edite o arquivo `.env` com suas credenciais de banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=example_db
DB_USERNAME=root
DB_PASSWORD=example_password
```

### 7. Execute as migrações

```bash
php artisan migrate
```

### 8. (Opcional) Popule o banco com dados de teste

```bash
php artisan db:seed --class=MarcaSeeder
php artisan db:seed --class=ModeloSeeder
```

### 9. Compile os assets do frontend

```bash
npm run build
```

### 10. Inicie o servidor Octane

```bash
php artisan octane:start
```

Acesse em: `http://localhost:8000`

## Arquitetura e Estrutura

O projeto segue a arquitetura MVC do Laravel, com camadas adicionais para separação de responsabilidades e melhor organização do código:

- **Repositories** para abstração e filtragem de dados.  

- **Actions** isolam regras de negócio e casos de uso, mantendo os controllers enxutos e focados no fluxo da aplicação.  

- **Testes Automatizados**: implementados com Pest para garantir confiabilidade e qualidade do código.

## Monitoramento

O **Laravel Telescope** é utilizado **apenas em ambiente de desenvolvimento** para inspecionar requisições, exceções, logs e queries, auxiliando no diagnóstico e na melhoria do desempenho da aplicação.

## Documentação da API

Este projeto utiliza o **Scribe** para gerar a documentação da API.  
A documentação é acessível em ambiente local e permite visualizar e testar os endpoints da aplicação.

### Acessar

Após iniciar o servidor, abra no navegador:

`http://localhost:8000/docs`

### Como interagir

A interface da documentação permite:

- Navegar por todos os endpoints organizados por módulos.
- Visualizar exemplos de requisição e resposta.
- Ver parâmetros obrigatórios e opcionais.
- Enviar requisições diretamente pela própria página (quando habilitado).

A documentação ainda está em evolução, mas já pode ser usada para consultar e testar a API durante o desenvolvimento.

## Status do Projeto

Este projeto ainda está em desenvolvimento ativo.
Novas funcionalidades, testes e melhorias de arquitetura estão sendo implementadas gradualmente.
