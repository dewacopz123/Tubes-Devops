import { describe, it, expect, vi, afterEach } from "vitest";
import { db } from "../../src/config/db.js";
import { AuditService } from "../../src/modules/audit/audit.service.js";
import { AuditRepository } from "../../src/modules/audit/audit.repository.js";
import { DevicesService } from "../../src/modules/devices/devices.service.js";
import { DevicesRepository } from "../../src/modules/devices/devices.repository.js";
import { PatientsService } from "../../src/modules/patients/patients.service.js";
import { PatientsRepository } from "../../src/modules/patients/patients.repository.js";
import { QueueService } from "../../src/modules/queue/queue.service.js";
import { QueueRepository } from "../../src/modules/queue/queue.repository.js";
import { FcmService } from "../../src/services/fcm.service.js";
import { blobService } from "../../src/services/blob.service.js";

describe("Backend Services Unit Tests", () => {
  afterEach(() => {
    vi.restoreAllMocks();
  });
  it("AuditService.list should calculate pagination and query repository", async () => {
    const mockRows = [{ id: 1, entity: "exams", aksi: "CREATE" }];
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(AuditRepository, "list").mockResolvedValue(mockRows);

    const result = await AuditService.list({ entity: "exams", limit: "10", page: "2" });

    expect(result.data).toEqual(mockRows);
    expect(result.meta.page).toBe(2);
    expect(result.meta.limit).toBe(10);
    expect(result.meta.hasPrev).toBe(true);
    expect(mockRelease).toHaveBeenCalled();
  });

  it("DevicesService.upsertToken should call repository and release connection", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(DevicesRepository, "upsert").mockResolvedValue(true);

    const result = await DevicesService.upsertToken({
      akun_id: 1,
      fcm_token: "mock-token-xyz",
      platform: "ANDROID",
    });

    expect(result).toEqual({ ok: true });
    expect(DevicesRepository.upsert).toHaveBeenCalledWith(mockConn, {
      akun_id: 1,
      fcm_token: "mock-token-xyz",
      platform: "ANDROID",
    });
    expect(mockRelease).toHaveBeenCalled();
  });

  it("PatientsService.validateDateRanges should validate ranges and throw on invalid dates", () => {
    expect(() => {
      PatientsService.validateDateRanges({
        dobStart: "1990-01-01",
        dobEnd: "2000-01-01",
      });
    }).not.toThrow();

    expect(() => {
      PatientsService.validateDateRanges({
        dobStart: "2005-01-01",
        dobEnd: "1995-01-01",
      });
    }).toThrow("Date of birth end date must be after start date");

    expect(() => {
      PatientsService.validateDateRanges({
        regStart: "2026-05-10",
        regEnd: "2026-05-01",
      });
    }).toThrow("Registration end date must be after start date");
  });

  it("PatientsService.list should return items and total count", async () => {
    const mockItems = [{ id: 1, nama: "Budi" }];
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(PatientsRepository, "list").mockResolvedValue(mockItems);
    vi.spyOn(PatientsRepository, "count").mockResolvedValue(1);

    const result = await PatientsService.list({ search: "Budi", page: 1, pageSize: 10 });

    expect(result.items).toEqual(mockItems);
    expect(result.total).toBe(1);
    expect(result.page).toBe(1);
    expect(mockRelease).toHaveBeenCalled();
  });

  it("PatientsService.detail should throw 404 when patient does not exist", async () => {
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(PatientsRepository, "findById").mockResolvedValue(null);

    await expect(PatientsService.detail(999)).rejects.toThrow("Patient not found");
    expect(mockRelease).toHaveBeenCalled();
  });

  it("QueueService.listToday should return queue list for given date", async () => {
    const mockData = [{ id: 1, no_antrian: 1, status: "MENUNGGU" }];
    const mockRelease = vi.fn();
    const mockConn = { release: mockRelease };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(QueueRepository, "listToday").mockResolvedValue(mockData);

    const result = await QueueService.listToday("2026-11-03");

    expect(result.tanggal).toBe("2026-11-03");
    expect(result.data).toEqual(mockData);
    expect(mockRelease).toHaveBeenCalled();
  });

  it("QueueService.call should transition status to DILAYANI and notify", async () => {
    const mockRelease = vi.fn();
    const mockBegin = vi.fn();
    const mockCommit = vi.fn();
    const mockRollback = vi.fn();
    const mockConn = {
      beginTransaction: mockBegin,
      commit: mockCommit,
      rollback: mockRollback,
      release: mockRelease,
    };

    vi.spyOn(db, "getConnection").mockResolvedValue(mockConn);
    vi.spyOn(QueueRepository, "getById").mockResolvedValue({ id: 5, status: "MENUNGGU" });
    vi.spyOn(QueueRepository, "updateStatus").mockResolvedValue(true);
    vi.spyOn(AuditRepository, "insert").mockResolvedValue(true);
    vi.spyOn(QueueRepository, "getDetailForNotify").mockResolvedValue({
      akun_id: 2,
      no_antrian: 5,
      pendaftaran_id: 5,
      tanggal_antrian: "2026-11-03",
    });
    vi.spyOn(FcmService, "notifyAkun").mockResolvedValue({ ok: true, sent: 1 });

    const result = await QueueService.call({ pendaftaranId: 5, actorAkunId: 1 });

    expect(result.status).toBe("DILAYANI");
    expect(mockCommit).toHaveBeenCalled();
    expect(mockRelease).toHaveBeenCalled();
  });

  it("QueueService.cancel should update status to DIBATALKAN", async () => {
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
    vi.spyOn(QueueRepository, "getById").mockResolvedValue({ id: 5, status: "MENUNGGU" });
    vi.spyOn(QueueRepository, "updateStatus").mockResolvedValue(true);
    vi.spyOn(AuditRepository, "insert").mockResolvedValue(true);

    const result = await QueueService.cancel({
      pendaftaranId: 5,
      actorAkunId: 1,
      reason: "Pasien tidak hadir",
    });

    expect(result.status).toBe("DIBATALKAN");
    expect(mockCommit).toHaveBeenCalled();
    expect(mockRelease).toHaveBeenCalled();
  });

  it("FcmService.notifyAkun should return skipped when FCM is not initialized", async () => {
    const result = await FcmService.notifyAkun({
      akun_id: 1,
      title: "Panggilan Antrian",
      body: "Nomor antrian Anda telah dipanggil",
    });

    expect(result.ok).toBe(false);
    expect(result.skipped).toBe(true);
    expect(result.reason).toBe("FCM not initialized");
  });

  it("blobService helper methods should parse SAS expiry and hash buffer correctly", () => {
    // SAS Expiry parsing
    expect(blobService._parseSasExpiry("5")).toBe(5);
    expect(blobService._parseSasExpiry("20")).toBe(15); // clamped to max 15
    expect(blobService._parseSasExpiry("invalid")).toBe(10); // fallback to 10
    expect(blobService._parseSasExpiry("-1")).toBe(10); // fallback

    // _clampExpiry
    expect(blobService._clampExpiry(0)).toBe(1);
    expect(blobService._clampExpiry(30)).toBe(15);
    expect(blobService._clampExpiry(8)).toBe(8);

    // SHA256 helper
    const testBuf = Buffer.from("hello eLabora");
    const hash = blobService.constructor.sha256(testBuf);
    expect(typeof hash).toBe("string");
    expect(hash.length).toBe(64);
  });

  it("blobService upload, exists, deleteBlob, and retry handling", async () => {
    const mockUploadData = vi.fn().mockResolvedValue(true);
    const mockDelete = vi.fn().mockResolvedValue(true);
    const mockExists = vi.fn().mockResolvedValue(true);

    const mockBlockClient = { uploadData: mockUploadData };
    const mockBlobClient = { exists: mockExists };
    const mockContainerClient = {
      getBlockBlobClient: vi.fn().mockReturnValue(mockBlockClient),
      getBlobClient: vi.fn().mockReturnValue(mockBlobClient),
      deleteBlob: mockDelete,
    };

    blobService.client = {
      getContainerClient: vi.fn().mockReturnValue(mockContainerClient),
    };

    // Test upload
    const up = await blobService.upload({
      container: "exam-results",
      blobName: "1/test.pdf",
      buffer: Buffer.from("test"),
      contentType: "application/pdf",
      originalFilename: "test.pdf",
    });
    expect(up.blobName).toBe("1/test.pdf");
    expect(mockUploadData).toHaveBeenCalled();

    // Test exists
    const ex = await blobService.exists({ container: "exam-results", blobName: "1/test.pdf" });
    expect(ex).toBe(true);

    // Test deleteBlob (success)
    await blobService.deleteBlob({ container: "exam-results", blobName: "1/test.pdf" });
    expect(mockDelete).toHaveBeenCalled();

    // Test deleteBlob (swallows 404)
    mockDelete.mockRejectedValueOnce(Object.assign(new Error("Not found"), { statusCode: 404 }));
    await expect(blobService.deleteBlob({ container: "c", blobName: "b" })).resolves.not.toThrow();

    // Test _withRetry retries on transient error (e.g. statusCode 503)
    let callCount = 0;
    const retryFn = async () => {
      callCount++;
      if (callCount === 1) {
        throw Object.assign(new Error("Service Unavailable"), { statusCode: 503 });
      }
      return "success";
    };
    const retryResult = await blobService._withRetry(retryFn);
    expect(retryResult).toBe("success");
    expect(callCount).toBe(2);
  });
});
