# adoPET

## Instalação
### Requisitos
1. XAMPP

Necessário para executar a aplicação PHP e o servidor MySQL
Versão recomendada: XAMPP 8.0+ (inclui PHP 8.0+ e MySQL)
Download: https://www.apachefriends.org/pt_br/index.html

2. Navegador Web

Chrome, Firefox, Edge ou qualquer navegador moderno

### Configuração do Ambiente
1. Instalação do XAMPP

Faça o download e instale o XAMPP
Durante a instalação, certifique-se de selecionar os componentes:
Apache
MySQL
PHP
phpMyAdmin

2. Configuração do Banco de Dados MySQL
Após instalar o XAMPP:

Inicie o Apache e o MySQL pelo painel de controle do XAMPP
Acesse o phpMyAdmin através do navegador: http://localhost/phpmyadmin
Crie um novo banco de dados com o nome dbAdoPET
Importe o arquivo SQL fornecido no projeto (adopet.sql) para criar as tabelas e popular os dados iniciais

### Credenciais padrão do MySQL no XAMPP:
Host: localhost (ou 127.0.0.1)
Porta: 3306
Usuário: root
Senha: (deixe em branco por padrão)
Banco de dados: dbAdoPET
Nota: Se você alterou a senha do usuário root no MySQL, será necessário atualizar as credenciais de conexão nos arquivos PHP do projeto.

### Clonagem do Projeto
1. Clone o repositório:
bashgit clone [URL_DO_REPOSITÓRIO]
2. Mova os arquivos para a pasta do XAMPP:

Copie a pasta do projeto para o diretório htdocs do XAMPP
Caminho padrão: C:\xampp\htdocs\ (Windows) ou /opt/lampp/htdocs/ (Linux)

3. Configure a conexão com o banco de dados:

Localize o arquivo de configuração de conexão (ex: config.php ou conexao.php)
Verifique se as credenciais do banco de dados estão corretas:

php$host = "localhost";
$usuario = "root";
$senha = ""; // senha padrão é vazia no XAMPP
$banco = "dbAdoPET";

### Clonagem do Projeto
1. Inicie os serviços:

Abra o painel de controle do XAMPP
Clique em Start nos módulos Apache e MySQL
Aguarde até que ambos exibam o status em verde

2. Acesse a aplicação:

Abra seu navegador
Digite na barra de endereços: http://localhost/[NOME_DA_PASTA_DO_PROJETO]
Exemplo: http://localhost/adopet

3. Comece a usar:

A página inicial da aplicação será carregada
Você pode fazer login ou criar uma nova conta conforme as funcionalidades disponíveis


Solução de Problemas
Apache não inicia:

Verifique se a porta 80 não está sendo usada por outro programa
Você pode alterar a porta do Apache no arquivo httpd.conf

MySQL não inicia:

Verifique se a porta 3306 não está sendo usada
Verifique se não há outra instância do MySQL rodando no sistema

Erro de conexão com o banco:

Confirme que o MySQL está rodando no XAMPP
Verifique as credenciais no arquivo de configuração PHP
Certifique-se de que o banco dbAdoPET foi criado e populado corretamente
