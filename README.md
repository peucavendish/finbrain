# FinBrain - Planejador de Independência Financeira

Sistema de análise financeira com IA para planejamento de independência financeira.

## Funcionalidades
- Interface web para preenchimento de dados pessoais, saúde, estilo de vida e histórico familiar
- Integração com OpenAI para análise e recomendação
- Não armazena dados do usuário no banco (apenas processamento em memória)

## Deploy na AWS

### 1. Criar uma instância EC2

1. Acesse o [Console AWS](https://aws.amazon.com/console/)
2. Vá para EC2 > Launch Instance
3. Selecione Ubuntu Server 22.04 LTS
4. Escolha t2.micro (ou maior se necessário)
5. Configure o Security Group:
   - HTTP (80): 0.0.0.0/0
   - HTTPS (443): 0.0.0.0/0
   - SSH (22): Seu IP
6. Crie ou selecione uma key pair para SSH
7. Lance a instância

### 2. Configurar DNS (opcional)

1. Registre um domínio (Route 53 ou outro registrador)
2. Crie um registro A apontando para o IP da sua instância
3. Aguarde a propagação do DNS

### 3. Conectar via SSH

```bash
chmod 400 sua-key.pem
ssh -i sua-key.pem ubuntu@seu-ip-ou-dominio
```

### 4. Preparar o servidor

1. Clone o repositório:
```bash
git clone seu-repositorio finbrain
cd finbrain
```

2. Configure as variáveis de ambiente:
```bash
# Edite .env.production com suas configurações
nano .env.production
```

3. Configure o script de deploy:
```bash
# Edite o script com suas configurações
nano deploy.sh
chmod +x deploy.sh
```

4. Execute o deploy:
```bash
./deploy.sh
```

### 5. Configurações pós-deploy

1. Configure SSL com Certbot:
```bash
sudo snap install --classic certbot
sudo ln -s /snap/bin/certbot /usr/bin/certbot
sudo certbot --nginx
```

2. Configure o backup do banco de dados:
```bash
# Instale o AWS CLI
sudo apt install awscli

# Configure suas credenciais
aws configure

# Crie um script de backup
sudo nano /etc/cron.daily/backup-mysql
```

3. Monitore os logs:
```bash
tail -f /var/log/nginx/error.log
tail -f storage/logs/laravel.log
```

## Manutenção

### Atualizar a aplicação

```bash
cd /var/www/finbrain
git pull
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo chown -R www-data:www-data .
```

### Backup do banco de dados

```bash
# Backup manual
mysqldump -u finbrain_user -p finbrain > backup.sql

# Restaurar backup
mysql -u finbrain_user -p finbrain < backup.sql
```

### Monitoramento

1. Configure o CloudWatch para monitorar:
   - Uso de CPU
   - Uso de memória
   - Espaço em disco
   - Logs do sistema

2. Configure alertas para:
   - Alto uso de recursos
   - Erros nos logs
   - Falhas de backup

## Segurança

1. Mantenha o sistema atualizado:
```bash
sudo apt update
sudo apt upgrade
```

2. Configure o firewall:
```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw enable
```

3. Boas práticas:
   - Use senhas fortes
   - Mantenha as chaves API seguras
   - Faça backup regularmente
   - Monitore os logs
   - Mantenha o PHP e dependências atualizados

## Suporte

Para suporte, entre em contato com a equipe de desenvolvimento.

---

## Licença
MIT
