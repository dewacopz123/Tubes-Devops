import { describe, it, expect, vi, afterEach } from "vitest";
import { db } from "../../src/config/db.js";
import { RegistrationService } from "../../src/modules/registrations/registration.service.js";
import { RegistrationRepository } from "../../src/modules/registrations/registration.repository.js";
import { AuditRepository } from "../../src/modules/audit/audit.repository.js";
import { blobService } from "../../src/services/blob.service.js";

describe("RegistrationService Unit Tests", () => {
  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("create should throw 400 when file is missing", async () => {
    await expect(
      RegistrationService.create({ akun_id: 1, file: null })
    ).rejects.toMatchObject({
      statusCode: 400,
      message: "Surat rujukan wajib diupload",
    });
  });

  it("create should throw 422 when file MIME or extension is disallowed", async () => {
    const invalidFile = {
      originalname: "malicious.exe",
      mimetype: "application/x-msdownload",
      size: 1024,
      buffer: Buffer.from("dummy"),
    };

    await expect(
      RegistrationService.create({ akun_id: 1, file: invalidFile })
    ).rejects.toMatchObject({
      statusCode: 422,
      message: "Format file tidak diizinkan",
    });
  });

  it("create should throw 422 when file exceeds 5MB", async () => {
    const hugeFile = {
      originalname: "huge.pdf",
      mimetype: "application/pdf",
      size: 6 * 1024 * 1024,
      buffer: Buffer.from("dummy"),
    };

    await expect(
      RegistrationService.create({ akun_id: 1, file: hugeFile })
    ).rejects.toMatchObject({
      statusCode: 422,
      message: "File size exceeds 5MB limit",
    });
  });

  it("create should upload blob and create registration successfully", async () => {
    const validFile = {
      originalname: "rujukan.pdf",
      mimetype: "application/pdf",
      size: 1024,
      buffer: Buffer.from("dummy pdf content"),
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

    vi.spyOn(blobService, "upload").mockResolvedValue({ etag: "mock-etag" });
    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(RegistrationRepository, "findPasienByAkunId").mockResolvedValue({ id: 8 });
    vi.spyOn(RegistrationRepository, "getLastQueueNumberForDate").mockResolvedValue(4);
    vi.spyOn(RegistrationRepository, "getNextPendaftaranId").mockResolvedValue(15);
    vi.spyOn(RegistrationRepository, "insertPendaftaran").mockResolvedValue(15);
    vi.spyOn(RegistrationRepository, "updateNoLab").mockResolvedValue(true);
    vi.spyOn(AuditRepository, "insert").mockResolvedValue(true);

    const result = await RegistrationService.create({
      akun_id: 1,
      tanggal_antrian: "2026-11-03",
      jadwal_pemeriksaan_at: "2026-11-03 10:00:00",
      file: validFile,
    });

    expect(result.id).toBe(15);
    expect(result.no_antrian).toBe("005");
    expect(result.status).toBe("MENUNGGU");
    expect(mockCommit).toHaveBeenCalled();
    expect(mockRelease).toHaveBeenCalled();
  });

  it("listMine should return registrations for current pasien", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(RegistrationRepository, "findPasienByAkunId").mockResolvedValue({ id: 8 });
    vi.spyOn(RegistrationRepository, "listByPasienId").mockResolvedValue([{ id: 15, no_antrian: 5 }]);

    const result = await RegistrationService.listMine({ akun_id: 1, tanggal: "2026-11-03" });

    expect(result.length).toBe(1);
    expect(result[0].id).toBe(15);
    expect(mockRelease).toHaveBeenCalled();
  });

  it("queueToday should return queue and stats", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(RegistrationRepository, "findPasienByAkunId").mockResolvedValue({ id: 8 });
    vi.spyOn(RegistrationRepository, "findMyQueueToday").mockResolvedValue({ no_antrian: 5 });
    vi.spyOn(RegistrationRepository, "getQueueStats").mockResolvedValue({ total: 10, waiting: 4 });

    const result = await RegistrationService.queueToday({ akun_id: 1 });

    expect(result.my.no_antrian).toBe(5);
    expect(result.stats.total).toBe(10);
    expect(mockRelease).toHaveBeenCalled();
  });

  it("downloadRujukan should check RBAC and return SAS url", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(RegistrationRepository, "findById").mockResolvedValue({
      id: 15,
      pasien_id: 8,
      surat_rujukan_blob_name: "2026/11/mock.pdf",
      surat_rujukan_container: "surat-rujukan",
      surat_rujukan_content_type: "application/pdf",
    });
    vi.spyOn(RegistrationRepository, "findPasienByAkunId").mockResolvedValue({ id: 8 });
    vi.spyOn(AuditRepository, "insert").mockResolvedValue(true);
    vi.spyOn(blobService, "exists").mockResolvedValue(true);
    vi.spyOn(blobService, "generateReadSas").mockResolvedValue({
      url: "https://azure.blob/surat-rujukan/2026/11/mock.pdf?sas=123",
      expiresAt: new Date().toISOString(),
    });

    const result = await RegistrationService.downloadRujukan({
      pendaftaranId: 15,
      user: { akun_id: 1, role: "PASIEN" },
    });

    expect(result.url).toBeDefined();
    expect(mockRelease).toHaveBeenCalled();
  });

  it("getByPatientId should validate input and return items", async () => {
    await expect(
      RegistrationService.getByPatientId({ patientId: "invalid" })
    ).rejects.toMatchObject({
      statusCode: 400,
      message: "Invalid patient ID",
    });

    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    const { PatientsRepository } = await import("../../src/modules/patients/patients.repository.js");

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(PatientsRepository, "findById").mockResolvedValue({ id: 8, nama: "Budi" });
    vi.spyOn(RegistrationRepository, "listByPatientIdWithPagination").mockResolvedValue([{ id: 10 }]);
    vi.spyOn(RegistrationRepository, "countByPatientId").mockResolvedValue(1);

    const res = await RegistrationService.getByPatientId({ patientId: 8, limit: 10 });
    expect(res.data.length).toBe(1);
    expect(res.meta.total).toBe(1);
    expect(mockRelease).toHaveBeenCalled();
  });
});
