import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import bcrypt from "bcrypt";
import jwt from "jsonwebtoken";
import { db } from "../../src/config/db.js";
import { AuthService } from "../../src/modules/auth/auth.service.js";
import { AuthRepository } from "../../src/modules/auth/auth.repository.js";

describe("AuthService Unit Tests", () => {
  beforeEach(() => {
    process.env.JWT_SECRET = "test-secret-key-123";
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("login should return token and role on valid credentials", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };
    const hash = await bcrypt.hash("secret123", 10);

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(AuthRepository, "findByUsername").mockResolvedValue({
      id: 1,
      username: "user1",
      password_hash: hash,
      role: "PASIEN",
    });

    const result = await AuthService.login("user1", "secret123");

    expect(result.role).toBe("PASIEN");
    expect(result.token).toBeDefined();
    const decoded = jwt.verify(result.token, process.env.JWT_SECRET);
    expect(decoded.akun_id).toBe(1);
    expect(decoded.role).toBe("PASIEN");
    expect(mockRelease).toHaveBeenCalled();
  });

  it("login should throw 401 when username not found", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(AuthRepository, "findByUsername").mockResolvedValue(null);

    await expect(AuthService.login("nonexistent", "secret")).rejects.toMatchObject({
      status: 401,
      message: "Username atau password salah",
    });
    expect(mockRelease).toHaveBeenCalled();
  });

  it("login should throw 401 on wrong password", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };
    const hash = await bcrypt.hash("correct-password", 10);

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(AuthRepository, "findByUsername").mockResolvedValue({
      id: 1,
      username: "user1",
      password_hash: hash,
      role: "PASIEN",
    });

    await expect(AuthService.login("user1", "wrong-password")).rejects.toMatchObject({
      status: 401,
      message: "Username atau password salah",
    });
    expect(mockRelease).toHaveBeenCalled();
  });

  it("registerPasien should throw 409 if username or email already exists", async () => {
    const mockRelease = vi.fn();
    const mockRollback = vi.fn();
    const mockConn = { release: mockRelease, rollback: mockRollback };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(AuthRepository, "checkUsernameOrEmailExists").mockResolvedValue(true);

    await expect(
      AuthService.registerPasien({ username: "existing", email: "existing@mail.com" })
    ).rejects.toMatchObject({
      status: 409,
      message: "Username atau email sudah digunakan",
    });
    expect(mockRelease).toHaveBeenCalled();
  });

  it("registerPasien should insert akun and pasien, then commit and return token", async () => {
    const mockRelease = vi.fn();
    const mockBegin = vi.fn();
    const mockCommit = vi.fn();
    const mockConn = {
      beginTransaction: mockBegin,
      commit: mockCommit,
      rollback: vi.fn(),
      release: mockRelease,
    };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(AuthRepository, "checkUsernameOrEmailExists").mockResolvedValue(false);
    vi.spyOn(AuthRepository, "insertAkun").mockResolvedValue(42);
    vi.spyOn(AuthRepository, "insertPasien").mockResolvedValue(10);

    const result = await AuthService.registerPasien({
      username: "newpasien",
      email: "pasien@mail.com",
      password: "Password123!",
      nik: "1234567890123456",
      nama: "Pasien Baru",
      jenis_kelamin: "L",
    });

    expect(result.akun_id).toBe(42);
    expect(result.role).toBe("PASIEN");
    expect(result.token).toBeDefined();
    expect(mockCommit).toHaveBeenCalled();
    expect(mockRelease).toHaveBeenCalled();
  });

  it("registerDokter should insert akun and dokter successfully", async () => {
    const mockRelease = vi.fn();
    const mockBegin = vi.fn();
    const mockCommit = vi.fn();
    const mockConn = {
      beginTransaction: mockBegin,
      commit: mockCommit,
      rollback: vi.fn(),
      release: mockRelease,
    };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(AuthRepository, "checkUsernameOrEmailExists").mockResolvedValue(false);
    vi.spyOn(AuthRepository, "insertAkunDokter").mockResolvedValue(55);
    vi.spyOn(AuthRepository, "insertDokter").mockResolvedValue(12);

    const result = await AuthService.registerDokter({
      username: "dr.andi",
      email: "andi@mail.com",
      password: "Password123!",
      nip: "198001012005011001",
      nama: "dr. Andi Sp.PK",
    });

    expect(result.akun_id).toBe(55);
    expect(result.role).toBe("DOKTER");
    expect(result.token).toBeDefined();
    expect(mockCommit).toHaveBeenCalled();
    expect(mockRelease).toHaveBeenCalled();
  });

  it("registerPetugas should insert akun and petugas successfully", async () => {
    const mockRelease = vi.fn();
    const mockBegin = vi.fn();
    const mockCommit = vi.fn();
    const mockConn = {
      beginTransaction: mockBegin,
      commit: mockCommit,
      rollback: vi.fn(),
      release: mockRelease,
    };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(AuthRepository, "checkUsernameOrEmailExists").mockResolvedValue(false);
    vi.spyOn(AuthRepository, "insertAkunPetugas").mockResolvedValue(77);
    vi.spyOn(AuthRepository, "insertPetugas").mockResolvedValue(15);

    const result = await AuthService.registerPetugas({
      username: "petugas1",
      email: "petugas@mail.com",
      password: "Password123!",
      nip: "199001012015011002",
      nama: "Siti Rahma",
    });

    expect(result.akun_id).toBe(77);
    expect(result.role).toBe("PETUGAS");
    expect(result.token).toBeDefined();
    expect(mockCommit).toHaveBeenCalled();
    expect(mockRelease).toHaveBeenCalled();
  });

  it("getMe should fetch akun and profile", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(AuthRepository, "findAkunById").mockResolvedValue({ id: 1, username: "user1" });
    vi.spyOn(AuthRepository, "findProfileByRole").mockResolvedValue({ id: 99, nama: "Budi" });

    const result = await AuthService.getMe(1, "PASIEN");

    expect(result.akun.username).toBe("user1");
    expect(result.profil.nama).toBe("Budi");
    expect(mockRelease).toHaveBeenCalled();
  });
});
