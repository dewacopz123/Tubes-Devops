import 'dotenv/config';
import mysql from 'mysql2/promise';

const poolConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    port: Number(process.env.DB_PORT) || 3306,
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'elabora_db',
    waitForConnections: true,
    connectionLimit: 10,
    dateStrings: true,
    typeCast: function (field, next) {
        if (field.type === 'DATETIME' || field.type === 'TIMESTAMP') {
            return field.string();
        }
        return next();
    }
};

// Aktifkan SSL hanya jika DB_SSL bernilai 'true' (mis. cloud database Aiven/Railway)
if (process.env.DB_SSL === 'true') {
    poolConfig.ssl = {
        rejectUnauthorized: false
    };
}

export const db = mysql.createPool(poolConfig);
