import { describe, it, expect, vi, afterEach } from "vitest";
import { db } from "../../src/config/db.js";
import { ExamsService } from "../../src/modules/exams/exams.service.js";
import { ExamsRepository } from "../../src/modules/exams/exams.repository.js";
import { AuditRepository } from "../../src/modules/audit/audit.repository.js";
import { blobService } from "../../src/services/blob.service.js";

describe("ExamsService Unit Tests", () => {
  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("listByPatient should return exams list", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(ExamsRepository, "listByPatient").mockResolvedValue([{ id: 1, no_lab: "LAB-001" }]);

    const result = await ExamsService.listByPatient(5);

    expect(result.length).toBe(1);
    expect(result[0].no_lab).toBe("LAB-001");
    expect(mockRelease).toHaveBeenCalled();
  });

  it("detail should return exam with files, or throw 404", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(ExamsRepository, "getDetail").mockResolvedValueOnce(null);

    await expect(ExamsService.detail(999)).rejects.toMatchObject({
      statusCode: 404,
      message: "Pemeriksaan not found",
    });

    vi.spyOn(ExamsRepository, "getDetail").mockResolvedValueOnce({ id: 1, no_lab: "LAB-001" });
    vi.spyOn(ExamsRepository, "listFiles").mockResolvedValue([{ id: 10, file_path: "path/to/file" }]);

    const result = await ExamsService.detail(1);
    expect(result.id).toBe(1);
    expect(result.files.length).toBe(1);
    expect(mockRelease).toHaveBeenCalled();
  });

  it("create should verify petugasLab and insert examination", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(ExamsRepository, "findPetugasLabIdByAkunId").mockResolvedValueOnce(null);

    await expect(
      ExamsService.create({ payload: {}, akunId: 99 })
    ).rejects.toThrow("Data petugas_lab tidak ditemukan untuk akun ini");

    vi.spyOn(ExamsRepository, "findPetugasLabIdByAkunId").mockResolvedValueOnce({ id: 3 });
    vi.spyOn(ExamsRepository, "create").mockResolvedValue(10);
    vi.spyOn(AuditRepository, "insert").mockResolvedValue(true);
    vi.spyOn(ExamsRepository, "getDetail").mockResolvedValue({ id: 10, no_lab: "LAB-010" });

    const created = await ExamsService.create({ payload: { pendaftaran_id: 1 }, akunId: 2 });
    expect(created.id).toBe(10);
    expect(mockRelease).toHaveBeenCalled();
  });

  it("update should update exam and insert audit log", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(ExamsRepository, "update").mockResolvedValue(true);
    vi.spyOn(AuditRepository, "insert").mockResolvedValue(true);
    vi.spyOn(ExamsRepository, "getDetail").mockResolvedValue({ id: 10, catatan: "Selesai" });

    const updated = await ExamsService.update(10, { catatan: "Selesai" }, 1);
    expect(updated.catatan).toBe("Selesai");
    expect(mockRelease).toHaveBeenCalled();
  });

  it("attachFile should validate file parameters and upload blob", async () => {
    await expect(
      ExamsService.attachFile({ pemeriksaanId: 10, file: null, akunId: 1 })
    ).rejects.toMatchObject({
      statusCode: 400,
      message: "File wajib diupload",
    });

    const invalidExtFile = {
      originalname: "test.exe",
      mimetype: "application/octet-stream",
      size: 100,
      buffer: Buffer.from("data"),
    };
    await expect(
      ExamsService.attachFile({ pemeriksaanId: 10, file: invalidExtFile, akunId: 1 })
    ).rejects.toMatchObject({
      statusCode: 422,
      message: "Format file tidak diizinkan",
    });

    const validFile = {
      originalname: "hasil.pdf",
      mimetype: "application/pdf",
      size: 500,
      buffer: Buffer.from("pdf-data"),
    };

    const mockRelease = vi.fn();
    const mockBegin = vi.fn();
    const mockCommit = vi.fn();
    const mockConn = {
      beginTransaction: mockBegin,
      commit: mockCommit,
      rollback: vi.fn(),
      release: mockRelease,
    };

    vi.spyOn(blobService, "upload").mockResolvedValue(true);
    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(ExamsRepository, "insertFile").mockResolvedValue(1);
    vi.spyOn(AuditRepository, "insert").mockResolvedValue(true);
    vi.spyOn(ExamsRepository, "listFiles").mockResolvedValue([{ id: 1, blob_name: "10/mock.pdf" }]);

    const files = await ExamsService.attachFile({ pemeriksaanId: 10, file: validFile, akunId: 1 });
    expect(files.length).toBe(1);
    expect(mockCommit).toHaveBeenCalled();
    expect(mockRelease).toHaveBeenCalled();
  });

  it("listAll should handle query filters and pagination", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(ExamsRepository, "listAll").mockResolvedValue([{ id: 1 }, { id: 2 }]);

    const res = await ExamsService.listAll({ page: 1, limit: 10 });
    expect(res.data.length).toBe(2);
    expect(res.meta.page).toBe(1);
    expect(res.meta.limit).toBe(10);
    expect(res.meta.hasNext).toBe(false);
    expect(mockRelease).toHaveBeenCalled();
  });
});
