import { describe, it, expect, vi, beforeEach } from "vitest";
import jwt from "jsonwebtoken";
import { requireAuth, requireRole } from "../../src/middleware/auth.middleware.js";
import { validate } from "../../src/middleware/validation.middleware.js";
import { notFound, errorHandler } from "../../src/middleware/error.middleware.js";
import Joi from "joi";

const TEST_SECRET = "test_super_secret_jwt_key_at_least_32_chars_long";

describe("Auth Middleware - requireAuth", () => {
  beforeEach(() => {
    process.env.JWT_SECRET = TEST_SECRET;
  });

  it("should return 401 when Authorization header is missing", () => {
    const req = { headers: {} };
    const res = {
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    requireAuth(req, res, next);

    expect(res.status).toHaveBeenCalledWith(401);
    expect(res.json).toHaveBeenCalledWith({ message: "Unauthorized" });
    expect(next).not.toHaveBeenCalled();
  });

  it("should return 401 when Authorization header does not start with Bearer", () => {
    const req = { headers: { authorization: "Basic token123" } };
    const res = {
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    requireAuth(req, res, next);

    expect(res.status).toHaveBeenCalledWith(401);
    expect(next).not.toHaveBeenCalled();
  });

  it("should return 401 when token is invalid or expired", () => {
    const req = { headers: { authorization: "Bearer invalid.token.value" } };
    const res = {
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    requireAuth(req, res, next);

    expect(res.status).toHaveBeenCalledWith(401);
    expect(res.json).toHaveBeenCalledWith({ message: "Invalid or expired token" });
    expect(next).not.toHaveBeenCalled();
  });

  it("should set req.user and call next when token is valid", () => {
    const payload = { akun_id: 10, role: "PASIEN" };
    const validToken = jwt.sign(payload, TEST_SECRET);

    const req = { headers: { authorization: `Bearer ${validToken}` } };
    const res = {
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    requireAuth(req, res, next);

    expect(req.user).toEqual({ akun_id: 10, role: "PASIEN" });
    expect(next).toHaveBeenCalled();
  });
});

describe("Auth Middleware - requireRole", () => {
  it("should return 403 when req.user is undefined", () => {
    const middleware = requireRole("DOKTER", "PETUGAS");
    const req = {};
    const res = {
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    middleware(req, res, next);

    expect(res.status).toHaveBeenCalledWith(403);
    expect(res.json).toHaveBeenCalledWith({ message: "Forbidden" });
    expect(next).not.toHaveBeenCalled();
  });

  it("should return 403 when req.user role does not match allowed roles", () => {
    const middleware = requireRole("DOKTER");
    const req = { user: { role: "PASIEN" } };
    const res = {
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    middleware(req, res, next);

    expect(res.status).toHaveBeenCalledWith(403);
    expect(next).not.toHaveBeenCalled();
  });

  it("should call next when req.user role matches allowed roles", () => {
    const middleware = requireRole("DOKTER", "PETUGAS");
    const req = { user: { role: "DOKTER" } };
    const res = {
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    middleware(req, res, next);

    expect(next).toHaveBeenCalled();
    expect(res.status).not.toHaveBeenCalled();
  });
});

describe("Validation Middleware - validate", () => {
  const schema = Joi.object({
    name: Joi.string().required(),
    age: Joi.number().min(0).required(),
  });

  it("should call next and strip unknown fields on valid payload", () => {
    const middleware = validate(schema);
    const req = { body: { name: "John", age: 25, extraField: "ignored" } };
    const res = {
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    middleware(req, res, next);

    expect(next).toHaveBeenCalled();
    expect(req.body).toEqual({ name: "John", age: 25 });
  });

  it("should return 400 with details on invalid payload", () => {
    const middleware = validate(schema);
    const req = { body: { age: -5 } };
    const res = {
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    middleware(req, res, next);

    expect(res.status).toHaveBeenCalledWith(400);
    expect(res.json).toHaveBeenCalledWith(
      expect.objectContaining({
        message: "Validation failed",
        errors: expect.any(Array),
      })
    );
    expect(next).not.toHaveBeenCalled();
  });
});

describe("Error Middleware - notFound and errorHandler", () => {
  it("should set status 404 and call next with route not found error", () => {
    const req = { method: "GET", originalUrl: "/non-existent" };
    const res = { status: vi.fn().mockReturnThis() };
    const next = vi.fn();

    notFound(req, res, next);

    expect(res.status).toHaveBeenCalledWith(404);
    expect(next).toHaveBeenCalledWith(expect.any(Error));
  });

  it("should handle error with proper status code and json response", () => {
    const err = new Error("Custom error");
    err.status = 400;
    const req = { path: "/test", method: "POST" };
    const res = {
      statusCode: 200,
      status: vi.fn().mockReturnThis(),
      json: vi.fn(),
    };
    const next = vi.fn();

    errorHandler(err, req, res, next);

    expect(res.status).toHaveBeenCalledWith(400);
    expect(res.json).toHaveBeenCalledWith(
      expect.objectContaining({
        message: expect.any(String),
      })
    );
  });
});

