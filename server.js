const express = require('express');
const mysql = require('mysql');
const nodemailer = require('nodemailer');
const cron = require('node-cron');

const app = express();
const port = 3000;

// Configuração da conexão com o banco de dados MySQL
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root', // Substitua pelo seu usuário do MySQL
    password: '', // Substitua pela sua senha do MySQL
    database: 'FitTrack' // Nome do seu banco de dados
});

// Conectando ao banco de dados
db.connect((err) => {
    if (err) {
        console.error('Erro ao conectar ao banco de dados:', err);
        return;
    }
    console.log('Conectado ao banco de dados FitTrack');
});

// Configuração do Nodemailer para envio de e-mails
const transporter = nodemailer.createTransport({
    service: 'gmail', // Serviço de e-mail utilizado
    auth: {
        user: 'suportefitrack@gmail.com', // E-mail de envio
        pass: 'phud exjy rsnj zayl' // Senha do e-mail
    }
});

// Função para verificar usuários inativos
async function checkInactiveUsers() {
    const thresholdDate = new Date();
    thresholdDate.setDate(thresholdDate.getMinutes() - 1); // Ajustar período de inatividade

    const query = 'SELECT email FROM users WHERE last_login < ?'; // Consulta para buscar e-mails
    db.query(query, [thresholdDate], (error, results) => {
        if (error) {
            console.error('Erro ao buscar usuários inativos:', error);
            return;
        }

        console.log('Usuários inativos encontrados:', results); // Log dos usuários inativos

        // Enviando e-mails para cada usuário inativo
        results.forEach(user => {
            sendInactiveEmail(user.email);
        });
    });
}

// Função para enviar e-mail de notificação para usuários inativos
function sendInactiveEmail(email) {
    const mailOptions = {
        from: 'suportefitrack@gmail.com', // E-mail de envio
        to: email, // E-mail do usuário inativo
        subject: 'Saudades de você no FitTrack!',
        text: 'Olá! Notamos que você não fez login no FitTrack há algum tempo. Estamos aqui para ajudar você a voltar a se exercitar e a cuidar da sua saúde!',
    };

    transporter.sendMail(mailOptions, (error, info) => {
        if (error) {
            return console.error('Erro ao enviar e-mail:', error);
        }
        console.log('E-mail enviado: ' + info.response);
    });
}

// Configuração do cron para rodar a verificação de inatividade diariamente
cron.schedule('0 0 * * *', () => { // A cada dia à meia-noite
    console.log('Verificando usuários inativos...');
    checkInactiveUsers();
});

// Chamada inicial para verificar usuários inativos imediatamente
checkInactiveUsers();

// Iniciando o servidor
app.listen(port, () => {
    console.log(`Servidor rodando em http://localhost:${port}`);
});
