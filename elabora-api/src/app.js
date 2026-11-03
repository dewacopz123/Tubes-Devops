import express from "express";
import cors from "cors";
import helmet from "helmet";
import morgan from "morgan";

import { db } from "./config/db.js";
import authRoutes from "./modules/auth/auth.routes.js";
import registrationRoutes from "./modules/registrations/registration.routes.js";
import patientsRoutes from "./modules/patients/patients.routes.js";
import queueRoutes from "./modules/queue/queue.routes.js";
import examsRoutes from "./modules/exams/exams.routes.js";
import auditRoutes from "./modules/audit/audit.routes.js";
import devicesRoutes from "./modules/devices/devices.routes.js";

import { notFound, errorHandler } from "./middleware/error.middleware.js";

const app = express();
app.set("trust proxy", 1);

// Parse CORS allowed origins from environment variable
const allowedOrigins = process.env.CORS_ALLOWED_ORIGINS
  ? process.env.CORS_ALLOWED_ORIGINS.split(",").map((origin) => origin.trim())
  : null;

// Configure CORS: use whitelist if CORS_ALLOWED_ORIGINS is provided, otherwise allow all
const corsOptions = allowedOrigins
  ? {
      origin: (origin, callback) => {
        // Allow requests with no origin (like mobile apps or Postman)
        if (!origin) return callback(null, true);

        if (allowedOrigins.includes(origin)) {
          callback(null, true);
        } else {
          callback(new Error("Not allowed by CORS"));
        }
      },
      credentials: true,
    }
  : undefined;

app.use(helmet());
app.use(cors(corsOptions));
app.use(express.json());
app.use(morgan("dev"));

app.get("/health", async (req, res) => {
  try {
    const [rows] = await db.query("SELECT 1 + 1 AS result");
    res.status(200).json({
      status: "ok",
      database: "connected",
      result: rows[0]?.result ?? 2,
    });
  } catch (error) {
    res.status(500).json({
      status: "error",
      database: "disconnected",
      message: error.message,
    });
  }
});

app.get("/", (req, res) => {
  res.json({ message: "eLabora API is running" });
});

app.use("/auth", authRoutes);
app.use("/registrations", registrationRoutes);
app.use("/patients", patientsRoutes);
app.use("/queue", queueRoutes);
app.use("/exams", examsRoutes);
app.use("/audit-logs", auditRoutes);
app.use("/devices", devicesRoutes);

app.use(notFound);
app.use(errorHandler);

export default app;
