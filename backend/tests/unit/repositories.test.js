import { describe, it, expect, vi } from "vitest";
import { DevicesRepository } from "../../src/modules/devices/devices.repository.js";
import { AuditRepository } from "../../src/modules/audit/audit.repository.js";

describe("Repositories Unit Tests", () => {
  it("DevicesRepository.getNextDeviceTokenId should return max + 1", async () => {
    const mockConn = {
      query: vi.fn().mockResolvedValue([[{ max_id: 10 }]]),
    };

    const nextId = await DevicesRepository.getNextDeviceTokenId(mockConn);
    expect(nextId).toBe(11);
    expect(mockConn.query).toHaveBeenCalledWith(
      "SELECT MAX(id) AS max_id FROM device_tokens FOR UPDATE"
    );
  });

  it("DevicesRepository.upsert should call query with generated id and params", async () => {
    const mockConn = {
      query: vi
        .fn()
        .mockResolvedValueOnce([[{ max_id: 5 }]]) // getNextDeviceTokenId
        .mockResolvedValueOnce([true]), // insert
    };

    await DevicesRepository.upsert(mockConn, {
      akun_id: 1,
      fcm_token: "fcm123",
      platform: "ANDROID",
    });

    expect(mockConn.query).toHaveBeenCalledTimes(2);
  });

  it("AuditRepository.getNextAuditLogId should return max + 1", async () => {
    const mockConn = {
      query: vi.fn().mockResolvedValue([[{ max_id: 20 }]]),
    };

    const nextId = await AuditRepository.getNextAuditLogId(mockConn);
    expect(nextId).toBe(21);
  });

  it("AuditRepository.insert should insert log entry with stringified JSON detail", async () => {
    const mockConn = {
      query: vi
        .fn()
        .mockResolvedValueOnce([[{ max_id: 0 }]]) // getNextAuditLogId
        .mockResolvedValueOnce([true]), // insert
    };

    await AuditRepository.insert(mockConn, {
      entity: "pemeriksaan",
      entity_id: 100,
      aksi: "CREATE",
      changed_by_akun_id: 1,
      detail: { note: "test log" },
    });

    expect(mockConn.query).toHaveBeenCalledTimes(2);
  });

  it("AuditRepository.list should query with or without entity filter", async () => {
    const mockConn = {
      query: vi.fn().mockResolvedValue([[{ id: 1, entity: "pendaftaran" }]]),
    };

    const rowsWithEntity = await AuditRepository.list(mockConn, {
      entity: "pendaftaran",
      limit: 10,
      offset: 0,
    });
    expect(rowsWithEntity.length).toBe(1);

    const rowsWithoutEntity = await AuditRepository.list(mockConn, {
      limit: 20,
      offset: 0,
    });
    expect(rowsWithoutEntity.length).toBe(1);
  });
});
