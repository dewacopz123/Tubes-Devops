import { describe, it, expect } from "vitest";
import {
  registerPasienSchema,
  registerDokterSchema,
  registerPetugasSchema,
  loginSchema,
} from "../../src/modules/auth/auth.validators.js";
import {
  advancedSearchSchema,
} from "../../src/modules/patients/patients.validators.js";
import {
  createRegistrationSchema,
} from "../../src/modules/registrations/registration.validators.js";

describe("Auth Validators", () => {
  it("should validate a valid patient registration payload", () => {
    const validData = {
      username: "pasientest",
      email: "pasien@example.com",
      password: "Password123!",
      nik: "1234567890123456",
      nama: "Pasien Test",
      jenis_kelamin: "L",
      tgl_lahir: "1995-05-15",
      alamat: "Jl. Merdeka No. 1",
      no_telepon: "081234567890",
    };

    const { error, value } = registerPasienSchema.validate(validData);
    expect(error).toBeUndefined();
    expect(value.username).toBe("pasientest");
  });

  it("should reject patient registration with invalid NIK length", () => {
    const invalidData = {
      username: "pasientest",
      email: "pasien@example.com",
      password: "Password123!",
      nik: "123", // too short
      nama: "Pasien Test",
      jenis_kelamin: "L",
    };

    const { error } = registerPasienSchema.validate(invalidData);
    expect(error).toBeDefined();
  });

  it("should reject weak passwords", () => {
    const weakPasswordData = {
      username: "doktertest",
      email: "dokter@example.com",
      password: "simple", // no special chars, numbers, uppercase
      nip: "198001012010011001",
      nama: "Dr. Budi",
    };

    const { error } = registerDokterSchema.validate(weakPasswordData);
    expect(error).toBeDefined();
  });

  it("should validate doctor registration correctly", () => {
    const validData = {
      username: "dokterbudi",
      email: "budi@example.com",
      password: "DoctorPass123#",
      nip: "198001012010011001",
      nama: "Dr. Budi",
    };

    const { error } = registerDokterSchema.validate(validData);
    expect(error).toBeUndefined();
  });

  it("should validate lab staff registration correctly", () => {
    const validData = {
      username: "petugassiti",
      email: "siti@example.com",
      password: "StaffPass123$",
      nip: "199002022015022002",
      nama: "Siti Petugas",
    };

    const { error } = registerPetugasSchema.validate(validData);
    expect(error).toBeUndefined();
  });

  it("should validate login credentials", () => {
    const validLogin = {
      username: "user123",
      password: "password123",
    };

    const { error } = loginSchema.validate(validLogin);
    expect(error).toBeUndefined();

    const missingPassword = { username: "user123" };
    const { error: errMissing } = loginSchema.validate(missingPassword);
    expect(errMissing).toBeDefined();
  });
});

describe("Patients Validators", () => {
  it("should validate advanced search patients query", () => {
    const query = { name: "Budi", page: 1, pageSize: 10 };
    const { error, value } = advancedSearchSchema.validate(query);
    expect(error).toBeUndefined();
    expect(value.name).toBe("Budi");
  });

  it("should reject invalid date range in search query", () => {
    const query = { dobStart: "15-05-1995" }; // invalid pattern
    const { error } = advancedSearchSchema.validate(query);
    expect(error).toBeDefined();
  });
});

describe("Registration Validators", () => {
  it("should validate a valid registration payload", () => {
    const payload = {
      jadwal_pemeriksaan_at: "2026-11-10 09:00:00",
      tanggal_antrian: "2026-11-10",
    };

    const { error, value } = createRegistrationSchema.validate(payload);
    expect(error).toBeUndefined();
    expect(value.tanggal_antrian).toBe("2026-11-10");
  });

  it("should reject invalid jadwal_pemeriksaan_at date", () => {
    const payload = {
      jadwal_pemeriksaan_at: "bukan-tanggal",
      tanggal_antrian: "2026-11-10",
    };

    const { error } = createRegistrationSchema.validate(payload);
    expect(error).toBeDefined();
  });
});
