# E-commerce Yasmim Sol

Projeto de e-commerce desenvolvido com Laravel (backend) e Vue.js (frontend).

## 🚀 Tecnologias

- **Backend:**
  - Laravel 10
  - PHP 8.2
  - MySQL
  - RESTful API

- **Frontend:**
  - Vue.js 3
  - TypeScript
  - Tailwind CSS
  - Pinia (Gerenciamento de Estado)

## 📋 Pré-requisitos

- PHP >= 8.2
- Composer
- Node.js >= 16
- npm ou yarn
- MySQL

## 🔧 Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/ecommercesol.git
cd ecommercesol
```

2. Instale as dependências do backend:
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

3. Instale as dependências do frontend:
```bash
cd frontend
npm install
npm run dev
```

4. Configure as variáveis de ambiente:
- Copie o arquivo `.env.example` para `.env` tanto no backend quanto no frontend
- Ajuste as configurações de banco de dados e outras variáveis conforme necessário

## 🛠️ Desenvolvimento

- Backend roda em: `http://localhost:8000`
- Frontend roda em: `http://localhost:5173`

## 📦 Estrutura do Projeto

```
ecommercesol/
├── backend/           # API Laravel
│   ├── app/
│   ├── database/
│   └── ...
└── frontend/         # Aplicação Vue.js
    ├── src/
    ├── public/
    └── ...
```

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes. 