# App Locadora de Carros Remember

> Um aplicativo web de aluguel de veículos. Desenvolvido e aprimorado em **Laravel** com base em um projeto anterior.

## Tecnologias Utilizadas

- **Framework:** Laravel (PHP)
- **Banco de Dados:** MySQL
- **Container:** Docker ( Com o BD em Mysql)
- **Servidor:** FrankenPHP ( Octane )
- **ORM:** Eloquent

## Pré-requisitos

- PHP 8.2+
- Node.js 22.13.5+

## Instalação

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

### 5. Configure o banco de dados

Edite o arquivo `.env` com suas credenciais de banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=example_db
DB_USERNAME=root
DB_PASSWORD=example_password
```

### 6. Execute as migrações

```bash
php artisan migrate
```

### 7. (Opcional) Popule o banco com dados de teste

```bash
php artisan db:seed --class=MarcaSeeder
php artisan db:seed --class=ModeloSeeder
```

### 8. Compile os assets frontend

```bash
npm run build
```

### 9. Inicie o servidor Octane

```bash
php artisan octane:start
```

Acesse em: `http://localhost:8000`
