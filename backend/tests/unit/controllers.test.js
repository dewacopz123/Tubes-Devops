import { describe, it, expect, vi, afterEach } from "vitest";
import { AuditController } from "../../src/modules/audit/audit.controller.js";
import { AuditService } from "../../src/modules/audit/audit.service.js";
import { DevicesController } from "../../src/modules/devices/devices.controller.js";
import { DevicesService } from "../../src/modules/devices/devices.service.js";
import { AuthController } from "../../src/modules/auth/auth.controller.js";
import { AuthService } from "../../src/modules/auth/auth.service.js";
import { RegistrationController } from "../../src/modules/registrations/registration.controller.js";
import { RegistrationService } from "../../src/modules/registrations/registration.service.js";
import {
  listPatients,
  getPatientDetail,
  advancedSearch,
  getPatientRegistrations,
} from "../../src/modules/patients/patients.controller.js";
import { PatientsService } from "../../src/modules/patients/patients.service.js";
import {
  listToday,
  callQueue,
  nextQueue,
  cancelQueue,
  queueStats,
} from "../../src/modules/queue/queue.controller.js";
import { QueueService } from "../../src/modules/queue/queue.service.js";
import {
  listByPatient,
  getDetail,
  createExam,
  patchExam,
  uploadExamFile,
  listAll as listAllExams,
} from "../../src/modules/exams/exams.controller.js";
import { ExamsService } from "../../src/modules/exams/exams.service.js";

describe("Controllers Unit Tests", () => {
  afterEach(() => {
    vi.restoreAllMocks();
  });

  const mockRes = () => {
    const res = {};
    res.status = vi.fn().mockReturnValue(res);
    res.json = vi.fn().mockReturnValue(res);
    return res;
  };

  it("AuditController.index should return audit list", async () => {
    vi.spyOn(AuditService, "list").mockResolvedValue({ data: [] });
    const req = { query: { entity: "exams" } };
    const res = mockRes();
    const next = vi.fn();

    await AuditController.index(req, res, next);
    expect(res.json).toHaveBeenCalledWith({ data: [] });
  });

  it("DevicesController.upsertToken should validate fcm_token and call service", async () => {
    const reqWithoutToken = { body: {} };
    const res = mockRes();
    const next = vi.fn();

    await DevicesController.upsertToken(reqWithoutToken, res, next);
    expect(res.status).toHaveBeenCalledWith(400);

    vi.spyOn(DevicesService, "upsertToken").mockResolvedValue({ ok: true });
    const reqWithToken = {
      body: { fcm_token: "token123" },
      user: { akun_id: 1 },
    };
    const res2 = mockRes();

    await DevicesController.upsertToken(reqWithToken, res2, next);
    expect(res2.json).toHaveBeenCalledWith({ ok: true });
  });

  it("AuthController endpoints should delegate to AuthService", async () => {
    const res = mockRes();
    const next = vi.fn();

    vi.spyOn(AuthService, "registerPasien").mockResolvedValue({ id: 1 });
    await AuthController.register({ body: {} }, res, next);
    expect(res.status).toHaveBeenCalledWith(201);

    vi.spyOn(AuthService, "registerDokter").mockResolvedValue({ id: 2 });
    await AuthController.registerDokter({ body: {} }, res, next);
    expect(res.status).toHaveBeenCalledWith(201);

    vi.spyOn(AuthService, "registerPetugas").mockResolvedValue({ id: 3 });
    await AuthController.registerPetugas({ body: {} }, res, next);
    expect(res.status).toHaveBeenCalledWith(201);

    vi.spyOn(AuthService, "login").mockResolvedValue({ token: "jwt" });
    await AuthController.login({ body: { username: "u", password: "p" } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ token: "jwt" });

    vi.spyOn(AuthService, "getMe").mockResolvedValue({ akun: {} });
    await AuthController.me({ user: { akun_id: 1, role: "PASIEN" } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ akun: {} });
  });

  it("RegistrationController methods should delegate to RegistrationService", async () => {
    const res = mockRes();
    const next = vi.fn();

    vi.spyOn(RegistrationService, "listMine").mockResolvedValue([]);
    await RegistrationController.listMine({ user: { akun_id: 1 }, query: {} }, res, next);
    expect(res.json).toHaveBeenCalledWith([]);

    vi.spyOn(RegistrationService, "queueToday").mockResolvedValue({ my: null });
    await RegistrationController.queueToday({ user: { akun_id: 1 } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ my: null });

    vi.spyOn(RegistrationService, "downloadRujukan").mockResolvedValue({ url: "https://sas" });
    await RegistrationController.downloadRujukan({ params: { id: "10" }, user: { akun_id: 1 } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ url: "https://sas" });
  });

  it("PatientsController functions should delegate to PatientsService", async () => {
    const res = mockRes();
    const next = vi.fn();

    // listPatients
    vi.spyOn(PatientsService, "list").mockResolvedValue({ items: [] });
    await listPatients({ query: {} }, res, next);
    expect(res.json).toHaveBeenCalledWith({ items: [] });

    // getPatientDetail
    vi.spyOn(PatientsService, "detail").mockResolvedValue({ id: 1 });
    await getPatientDetail({ params: { id: "1" } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ id: 1 });

    // advancedSearch
    vi.spyOn(PatientsService, "advancedSearch").mockResolvedValue({ items: [] });
    await advancedSearch({ body: { name: "Budi" } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ items: [] });
  });

  it("QueueController functions should delegate to QueueService", async () => {
    const res = mockRes();
    const next = vi.fn();

    vi.spyOn(QueueService, "listToday").mockResolvedValue({ data: [] });
    await listToday({ query: {} }, res, next);
    expect(res.json).toHaveBeenCalledWith({ data: [] });

    vi.spyOn(QueueService, "call").mockResolvedValue({ id: 1, status: "DILAYANI" });
    await callQueue({ params: { id: "1" }, user: { akun_id: 1 } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ id: 1, status: "DILAYANI" });

    vi.spyOn(QueueService, "next").mockResolvedValue({ id: 1, status: "SELESAI" });
    await nextQueue({ params: { id: "1" }, user: { akun_id: 1 } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ id: 1, status: "SELESAI" });

    vi.spyOn(QueueService, "cancel").mockResolvedValue({ id: 1, status: "DIBATALKAN" });
    await cancelQueue({ params: { id: "1" }, body: {}, user: { akun_id: 1 } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ id: 1, status: "DIBATALKAN" });
  });

  it("ExamsController functions should delegate to ExamsService", async () => {
    const res = mockRes();
    const next = vi.fn();

    vi.spyOn(ExamsService, "listByPatient").mockResolvedValue([]);
    await listByPatient({ params: { pasienId: "1" } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ data: [] });

    vi.spyOn(ExamsService, "detail").mockResolvedValue({ id: 1 });
    await getDetail({ params: { id: "1" } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ id: 1 });

    vi.spyOn(ExamsService, "create").mockResolvedValue({ id: 1 });
    await createExam({ body: {}, user: { akun_id: 1 } }, res, next);
    expect(res.status).toHaveBeenCalledWith(201);

    vi.spyOn(ExamsService, "update").mockResolvedValue({ id: 1, catatan: "ok" });
    await patchExam({ params: { id: "1" }, body: { catatan: "ok" }, user: { akun_id: 1 } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ id: 1, catatan: "ok" });

    // uploadExamFile without file should 422
    const res422 = mockRes();
    await uploadExamFile({ params: { id: "1" }, user: { akun_id: 1 } }, res422, next);
    expect(res422.status).toHaveBeenCalledWith(422);

    vi.spyOn(ExamsService, "attachFile").mockResolvedValue([{ id: 1 }]);
    await uploadExamFile({ params: { id: "1" }, file: { originalname: "a.pdf" }, user: { akun_id: 1 } }, res, next);
    expect(res.json).toHaveBeenCalledWith({ files: [{ id: 1 }] });

    vi.spyOn(ExamsService, "listAll").mockResolvedValue({ data: [] });
    await listAllExams({ query: {} }, res, next);
    expect(res.json).toHaveBeenCalledWith({ data: [] });
  });
});
