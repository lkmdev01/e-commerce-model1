FROM node:18-alpine

WORKDIR /app

# Copiar apenas os arquivos necessários para instalar dependências
COPY package*.json ./

# Limpar cache do npm e instalar dependências
RUN npm cache clean --force && \
  npm install --no-audit --no-fund --prefer-offline

# Copiar o resto dos arquivos
COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"] 