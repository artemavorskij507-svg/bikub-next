# Security Audit Export Retention Policy

Security governance exports are JSON evidence packages stored on the private local disk. Downloads require authentication, explicit permission, and successful SHA-256 verification.

- Exports exclude secrets, passwords, decrypted payout data, bank/IBAN/Vipps data, private evidence paths, and raw evidence files.
- Archive is non-destructive and requires an authorized actor and reason.
- Default retention should be reviewed annually; scheduling retention does not delete a file.
- Physical deletion requires a separate approved retention job and is not implemented here.
- External sharing is not implemented.
- Access and downloads are audited.

Privacy and GDPR obligations require qualified legal review before production retention periods are adopted; this document is a product control, not legal advice.
