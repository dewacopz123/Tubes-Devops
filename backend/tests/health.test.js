import { describe, it, expect } from 'vitest';
import request from 'supertest';
import app from '../src/app.js';

describe('eLabora API Baseline Test Suite', () => {
  it('GET / should return 200 and welcoming JSON message', async () => {
    const response = await request(app).get('/');
    expect(response.status).toBe(200);
    expect(response.body).toHaveProperty('message');
    expect(response.body.message).toBe('eLabora API is running');
  });

  it('GET /non-existent-route should return 404 Not Found', async () => {
    const response = await request(app).get('/api/unregistered-endpoint-xyz');
    expect(response.status).toBe(404);
    expect(response.body).toHaveProperty('message');
  });
});
